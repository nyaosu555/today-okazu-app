<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Models\Menu;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use AuthorizesRequests;

    // メニュー登録画面を表示
    public function create() {
        return view('menus.create');
    }

    // 新しいメニューを登録
    public function store(StoreMenuRequest $request) {
        $validated = $request->validated();

        try {
            $imagePath = null;
            $imageStatus = $request->input('current_image_status');

            // ✕ボタンで削除された場合
            if ($imageStatus === 'deleted') {
                $imagePath = null;
            }
            // 新しい通常ファイルがアップロードされた場合
            elseif ($request->hasFile('image_path')) {
                $imagePath = $request->file('image_path')->store('menu_images', 'public');
            }
            // ファイルは空だが、裏に退避した画像データがある場合
            elseif (($imageStatus === 'fallback' || $imageStatus === 'new_image') && $request->filled('buffered_image_data')) {
                // Base64文字列から画像ファイルを復元する
                $base64Data = $request->input('buffered_image_data');

                // data:image/png;base64,xxxxxx のようなヘッダーを分解
                @list($type, $fileData) = explode(';', $base64Data);
                @list(, $fileData)      = explode(',', $fileData);

                if ($fileData) {
                    // 拡張子の判別
                    $extension = 'jpg';
                    if (str_contains($type, 'image/png'))  $extension = 'png';
                    if (str_contains($type, 'image/gif'))  $extension = 'gif';
                    if (str_contains($type, 'image/jpeg')) $extension = 'jpg';

                    // ユニークなファイル名を生成して保存
                    $fileName = 'menu_' . uniqid() . '.' . $extension;
                    $imagePath = 'menu_images/' . $fileName;

                    Storage::disk('public')->put($imagePath, base64_decode($fileData));
                }
            }

            // ログインユーザーに紐づけてメニューをデータベースに保存
            Auth::user()->menus()->create([
                'name'          => $validated['menu_name'],
                'type_id'       => $validated['type_id'],
                'image_path'    => $imagePath,
                'recipe_url'    => $validated['recipe_url'] ?? null,
                'memo'          => $validated['memo'] ?? null,
            ]);

            return redirect()->route('menus.index')->with([
                'message' => 'メニューを登録しました',
                'type' => 'success',
            ]);

        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            Log::error('メニュー登録失敗', [
                'user_id'       =>  Auth::id(),
                'error'         =>  $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with([
                'message'   =>  '登録中にエラーが発生しました。',
                'type'      =>  'danger',
            ]);
        }
    }

    // 登録メニュー一覧の表示
    public function index(Request $request) {
        // 現在ログインしているユーザー情報を取得
        $user = Auth::user();

        // 何ページ目かのセッションを残しておく（更新キャンセル用）
        session(['menus_current_page' => $request->query('page', 1)]);

        // 権限に応じて取得するメニューを分岐
        if($user->role === 'admin') {
            // 管理者の場合：すべてのユーザーのメニューを取得
            $menus = Menu::with(['user', 'type'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(12);
        } else {
            $menus = $user->menus()
                    ->with('type')
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);
        }

        // 登録フォーム用セレクトボックス用データ
        $types = Type::all();

        return view('menus.index', compact('menus', 'types'));
    }

    // メニューの編集画面表示
    public function edit(Menu $menu) {
        try {
            // Laravelが自動的に MenuPolicy の delete メソッドを呼び出してチェックする
            // 権限がない場合は自動的に「catch」に入る
            $this->authorize('view', $menu);

            // httpから始まる外部URLならそのまま使い、それ以外ならstorageを頭につける
            if ($menu->image_path) {
                $imagePath = str_starts_with($menu->image_path, 'http')
                    ? $menu->image_path
                    : asset('storage/' . $menu->image_path);
            } else {
                $imagePath = asset('images/no_image.png');
            }

            // 登録フォームと同様に、セレクトボックスのデータを取得
            $types = Type::all();

            // セッションからページ数を取得（なければ 1）
            $page = session('menus_current_page', 1);

            // 「キャンセル」で使ったら、もう不要なのでセッションから削除する
            session()->forget('menus_current_page');


            return view('menus.edit', compact('menu', 'types', 'imagePath', 'page'));

        } catch (AuthorizationException $e) {
                // 権限エラー（403）が発生した場合はここへジャンプします！
                Log::warning("不審なアクセス: ユーザーID " . auth()->id() . " がメニューID " . $menu->id . " の編集画面を開こうとしました。");

                return redirect()->route('menus.index')->with([
                    'message' => '編集する権限がありません。',
                    'type' => 'danger', // 赤色のFlashメッセージにする場合
                ]);

            } catch (\Throwable $e) {
                Log::error('メニュー編集失敗', [
                    'user_id'   =>  Auth::id(),
                    'menu_id'   =>  $menu->id,
                    'error'     =>  $e->getMessage(),
                    'trace'     =>  $e->getTraceAsString(),
                ]);

                return redirect()->route('menus.index')->with([
                    'message'   =>  '編集中にエラーが発生しました。時間をおいて再度お試しください。',
                    'type'      => 'danger',
                ]);
        }

    }

    // 登録メニュー編集処理
    /**
     * メニューの更新処理
     */
    public function update(StoreMenuRequest $request, Menu $menu) {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // ロールバック・変更比較用に「現在の画像パス」を退避
        $oldImagePath = $menu->image_path;
        $imagePath = $oldImagePath; // 初期値は現在の画像を維持

        try {
            // 堅牢なセキュリティ：tryの一番最初でPolicy（update）を使って権限をチェック
            // 権限がない場合は自動的に例外が発生し、安全にcatchへジャンプしてロールバックされます
            $this->authorize('update', $menu);

            // JavaScript側から送られてくる画像ステータスを取得
            $imageStatus = $request->input('current_image_status');

            // -------------------------------------------------------------
            // 画像の処理分岐
            // -------------------------------------------------------------

            // ユーザーがプレビューの「✕ボタン」を明示的に押して削除した場合
            if ($imageStatus === 'deleted') {
                $imagePath = null;
            }

            // 新しい画像ファイルが正常にアップロードされた場合
            elseif ($request->hasFile('image_path')) {
                $imagePath = $request->file('image_path')->store('menu_images', 'public');
            }

            // ファイル選択が空だが、裏に退避した画像データ（Base64）がある場合
            elseif (($imageStatus === 'fallback' || $imageStatus === 'new_image') && $request->filled('buffered_image_data')) {
                $base64Data = $request->input('buffered_image_data');

                // Base64のヘッダーとデータ本体を分離
                @list($type, $fileData) = explode(';', $base64Data);
                @list(, $fileData)      = explode(',', $fileData);

                if ($fileData) {
                    // 拡張子の判別
                    $extension = 'jpg';
                    if (str_contains($type, 'image/png'))  $extension = 'png';
                    if (str_contains($type, 'image/gif'))  $extension = 'gif';
                    if (str_contains($type, 'image/jpeg')) $extension = 'jpg';

                    // 一意のファイル名を生成して保存
                    $fileName = 'menu_' . uniqid() . '.' . $extension;
                    $imagePath = 'menu_images/' . $fileName;

                    Storage::disk('public')->put($imagePath, base64_decode($fileData));
                }
            }

            // -------------------------------------------------------------
            // データベースの更新
            // -------------------------------------------------------------
            $menu->update([
                'name'          => $validated['menu_name'],
                'type_id'       => $validated['type_id'],
                'image_path'    => $imagePath,
                'recipe_url'    => $validated['recipe_url'] ?? null, // カラムに合わせて調整してください
                'memo'          => $validated['memo'] ?? null,       // カラムに合わせて調整してください
            ]);

            // -------------------------------------------------------------
            //  古い画像ファイルのストレージ削除（完全に更新成功した時のみ実行）
            // -------------------------------------------------------------
            // 「元々画像があり」、かつ「画像が別のものに変わった、または削除された」場合
            if ($oldImagePath && $oldImagePath !== $imagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }

            // 一覧画面へリダイレクト（成功メッセージ付き）
            return redirect()->route('menus.index')->with([
                'message' => 'メニューを更新しました',
                'type' => 'success',
            ]);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // 💡 権限エラー（403）のキャッチ
            Log::warning("不審なアクセス: ユーザーID " . auth()->id() . " がメニューID " . $menu->id . " の更新処理（update）を不正に実行しようとしました。");

            return redirect()->route('menus.index')->with([
                'message' => '更新する権限がありません。',
                'type' => 'danger',
            ]);

        } catch (\Throwable $e) {
            // 予期せぬエラー（DBエラーなど）が発生した場合の安全なロールバック
            // この更新処理中に「新しく保存してしまった画像」があれば、ゴミとして残さないように削除
            if ($imagePath && $imagePath !== $oldImagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            // エラーログを詳細に出力
            Log::error('メニュー更新失敗', [
                'menu_id' => $menu->id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with([
                'message' => '更新中にエラーが発生しました。時間をおいて再度お試しください。',
                'type'    => 'danger',
            ]);
        }
    }

    // メニューの削除処理
    public function destroy(Menu $menu) {
        try {
            // Laravelが自動的に MenuPolicy の delete メソッドを呼び出してチェックする
            // 権限がない場合は自動的に「catch」に入る
            $this->authorize('delete', $menu);

            // データベースから削除
            $menu->delete();


            // サーバー上の画像を削除
            if($menu->image_path) {
                Storage::disk('public')->delete($menu->image_path);
            }


            // 一覧画面に戻す
            return redirect()->route('menus.index')->with([
                'message' => 'メニューを削除しました。',
                'type' => 'success',
            ]);

        } catch (AuthorizationException $e) {
            // 権限エラー（403）が発生した場合はここへジャンプします！
            Log::warning("不審なアクセス: ユーザーID " . auth()->id() . " がメニューID " . $menu->id . " を削除しようとしました。");

            return redirect()->route('menus.index')->with([
                'message' => '削除する権限がありません。',
                'type' => 'danger', // 赤色のFlashメッセージにする場合
            ]);

        } catch (\Throwable $e) {
            Log::error('メニュー削除失敗', [
                'user_id'   =>  Auth::id(),
                'menu_id'   =>  $menu->id,
                'error'     =>  $e->getMessage(),
                'trace'     =>  $e->getTraceAsString(),
            ]);

            return redirect()->route('menus.index')->with([
                'message'   =>  '削除中にエラーが発生しました。時間をおいて再度お試しください。',
                'type'      => 'danger',
            ]);
        }
    }

    // 入力された文字から似たメニューを検索してJSONJSONで返す（API用）
    public function searchSimilar(Request $request) {
        // そもそもログインしていない場合は、安全のために空の配列を返す
        if (!Auth::check()) {
            return response()->json([]);
        }
        // フロント（axios）から送られてきた検索キーワード（keyword）を取得
        $keyword = $request->input('keyword');

        // キーワードが空の場合は、空の配列を返す
        if(empty($keyword)) {
            return response()->json([]);
        }

        // ログインユーザーに紐づくメニューの中から、キーワードに「部分一致」するものを検索
        // SQLの LIKE 検索（%キーワード%）を使って、前後の表記ゆれをまとめて引っ掛ける
        $similarMenus = Auth::user()->menus()
            ->where('name', 'LIKE', '%'. $keyword . '%')
            ->get(['id', 'name', 'image_path']);

        Log::info('ヒットした件数: ' . $similarMenus->count());

        // 検索結果をそのままJSON形式でフロントに返却
        return response()->json($similarMenus);

    }
}
