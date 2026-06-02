import axios from "axios";

// 💡 グローバル変数の宣言（要素の取得は関数内で行うため、ここでは値だけを管理）
let lastValidSrc = null;
let fileName = '画像が選択されていません。';

// window.previewImage の中身を以下にアップデートしてください

window.previewImage = function(input) {
    console.log('画像選択（changeイベント）が発火しました');

    const preview = document.getElementById('imagePreview');
    const placeHolder = document.getElementById('placeholder');
    const deleteBtn = document.getElementById('deleteImageBtn');
    const laravelErrorMsg = document.querySelector('.laravel-image-error');
    const errorMsg = document.getElementById('jsImageError');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    const statusInput = document.getElementById('currentImageStatus');
    const bufferedDataInput = document.getElementById('bufferedImageData');

    const file = input.files[0];

    if (errorMsg) errorMsg.textContent = '';
    if (laravelErrorMsg) laravelErrorMsg.style.display = 'none';

    // =========================
    // チェック①：ファイルが存在しない（キャンセル時）
    // =========================
    if (!file) {
        console.log('画像選択キャンセル判定を通過');

        if (lastValidSrc) {
            if (preview) {
                preview.src = lastValidSrc;
                preview.classList.remove('hidden');
            }
            if (deleteBtn) deleteBtn.classList.remove('hidden');
            if (placeHolder) placeHolder.classList.add('hidden');
            if (fileNameDisplay) fileNameDisplay.textContent = fileName;

            // 💡 キャンセルされても、裏に保存した画像データを維持する
            if (statusInput) statusInput.value = 'fallback';
        } else {
            if (preview) {
                preview.src = '';
                preview.classList.add('hidden');
            }
            if (deleteBtn) deleteBtn.classList.add('hidden');
            if (placeHolder) placeHolder.classList.remove('hidden');
            if (fileNameDisplay) fileNameDisplay.textContent = '画像が選択されていません。';

            if (statusInput) statusInput.value = 'no_change';
            if (bufferedDataInput) bufferedDataInput.value = '';
        }
        return;
    }

    // =========================
    // チェック②：ファイルが存在する場合のバリデーション（★ここへ移動！）
    // =========================
    const maxSize = 2 * 1024 * 1024;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    let errorMessage = '';

    if (!allowedTypes.includes(file.type)) {
        errorMessage = '画像ファイルは（jpg, jpeg, png, gif）の中から選択してください。';
    } else if (file.size > maxSize) {
        errorMessage = '画像サイズは2MB以下にしてください。';
    }

    if (errorMessage) {
        if (errorMsg) errorMsg.textContent = errorMessage;
        if (lastValidSrc) {
            if (preview) {
                preview.src = lastValidSrc;
                preview.classList.remove('hidden');
            }
            if (deleteBtn) deleteBtn.classList.remove('hidden');
            if (placeHolder) placeHolder.classList.add('hidden');
            if (fileNameDisplay) fileNameDisplay.textContent = fileName;
        }
        return;
    }

    // =========================
    // 正常系：すべてのチェックを通過した安全なデータのみスキャン
    // =========================
    const reader = new FileReader();
    reader.onload = function(e) {
        console.log('画像読み込み成功');

        lastValidSrc = e.target.result;
        fileName = file.name;

        if (preview) {
            preview.src = lastValidSrc;
            preview.classList.remove('hidden');
        }
        if (deleteBtn) deleteBtn.classList.remove('hidden');
        if (placeHolder) placeHolder.classList.add('hidden');
        if (fileNameDisplay) fileNameDisplay.textContent = fileName;

        if (statusInput) statusInput.value = 'new_image';
        // 【超重要】隠しタグに画像のテキストデータを退避！
        if (bufferedDataInput) bufferedDataInput.value = e.target.result;
    };

    reader.readAsDataURL(file);
};

// ✕ボタンが押されたとき（画像をクリア）
window.clearImage = function() {

    // 省略せずにすべての要素を確実に取得します
    const input = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');
    const placeHolder = document.getElementById('placeholder');
    const deleteBtn = document.getElementById('deleteImageBtn');
    const errorMsg = document.getElementById('jsImageError');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const statusInput = document.getElementById('currentImageStatus');
    const bufferedDataInput = document.getElementById('bufferedImageData');

    // 選択されているファイルをリセット
    if (input) {
        input.value = '';
    }

    lastValidSrc = null;          // 保持していた画像データも消す
    fileName = '画像が選択されていません。';

    if (fileNameDisplay) fileNameDisplay.textContent = fileName;
    if (errorMsg) errorMsg.textContent = '';

    if (preview) {
        preview.src = '';
        preview.classList.add('hidden');
    }
    if (deleteBtn) deleteBtn.classList.add('hidden');
    if (placeHolder) placeHolder.classList.remove('hidden');

    // 明示的に「削除された」状態をセット
    if (statusInput) statusInput.value = 'deleted';
    if (bufferedDataInput) bufferedDataInput.value = ''; // テキストデータもクリア
};

// DOM構築後のイベントリスナー設定
document.addEventListener('DOMContentLoaded', () => {
    const menuForm = document.getElementById('menu-form');
    const saveMenuBtn = document.getElementById('save-menu-btn');
    const deleteBtns = document.querySelectorAll('.delete-individual-btn');

    // メニュー登録処理
    if(saveMenuBtn && menuForm) {
        saveMenuBtn.addEventListener('click', (e) => {
            e.preventDefault();

            // ボタンの文字「追加」「更新」かを読み取る
            const btnText = saveMenuBtn.textContent.trim();

            // 文字に「更新」が含まれているかどうかで、メッセージを切り替える
            let confirmMessage = 'この内容でメニューを登録しますか？';
            let confirmBtnText = '登録する';

            if(btnText.includes('更新')) {
                confirmMessage = 'この内容でメニューを更新しますか？';
                confirmBtnText = '更新する';
            }

            // カスタムイベントを発火
             window.dispatchEvent(new CustomEvent('confirm-delete', {
                detail: {
                    message: confirmMessage,
                    btnText: confirmBtnText,
                    action: () => {
                        menuForm.submit();
                    }
                }
            }));
        });
    }

    // メニュー削除処理
    if (deleteBtns.length > 0) {
        deleteBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                const menuName = btn.dataset.name;
                const form = btn.closest('.delete-menu-form');

                window.dispatchEvent(new CustomEvent('confirm-delete', {
                    detail: {
                        message: `${menuName}を削除します。よろしいですか？`,
                        btnText: '削除する',
                        action: () => {
                            form.submit();
                        }
                    }
                }));
            });
        });
    }

    // フラッシュメッセージ制御
    const messageBox = document.getElementById('flash-message');
    if (messageBox && messageBox.textContent.trim() !== '') {
        requestAnimationFrame(() => {
            messageBox.classList.remove('-translate-y-full');
            messageBox.classList.add('translate-y-0');
            messageBox.classList.add('opacity-100');
        });

        setTimeout(() => {
            messageBox.classList.remove('opacity-100', 'translate-y-0');
            messageBox.classList.add('opacity-0', '-translate-y-full');
            setTimeout(() => {
                messageBox.remove();
            }, 500);
        }, 7000);
    }

    const menuNameInput = document.getElementById('menu_name');
    const container = document.getElementById('similar-menus-container');
    const listContainer = document.getElementById('similar-menus-list');

    // 要素が存在しない画面でのエラーを防ぐ
    if(!menuNameInput || !container || !listContainer) return;

    let debounceTimeout;

    // 入力欄に文字が打ち込まれる度に発火
    menuNameInput.addEventListener('input', function() {
        const keyword = this.value.trim();


        // タイマーを毎回リセット
        clearTimeout(debounceTimeout);

        // キーワードがからならカードエリアを隠して終了
        if(!keyword) {
            container.classList.add('hidden');
            listContainer.innerHTML = '';
            return;
        }

        // 入力が終わって300ms後にAPIを実行（デバウンス処理）
        debounceTimeout = setTimeout(() => {
            axios.get('/api/menus/search-similar', {
                params: { keyword: keyword }
            })
            .then(response => {
                const menus = response.data;

                // 似たメニューが1件もなければ非表示
                if(menus.length === 0) {
                    container.classList.add('hidden');
                    listContainer.innerHTML = '';
                    return;
                }

                // 既存のリストをクリアして再構築
                listContainer.innerHTML = '';
                container.classList.remove('hidden');

                // 取得したメニューの数だけカード（HTML）を生成
                menus.forEach(menu => {
                    const card = document.createElement('div');
                    card.className = 'flex items-center gap-2 p-2 bg-white rounded border border-gray-200 shadow-sm text-sm';

                    // 画像があるかどうかの判定
                    // httpから始まっている（URLである）場合はそのまま使い、それ以外は /storage/ を足す
                    let imgSrc = '/images/no_image.png';
                    if (menu.image_path) {
                        imgSrc = menu.image_path.startsWith('http') ? menu.image_path : `/storage/${menu.image_path}`;
                    }

                    card.innerHTML = `
    <img src="${imgSrc}"
         onerror="this.onerror=null; this.src='/images/no_image.png';"
         class="w-10 h-10 object-cover rounded-md border border-gray-100 shrink-0"
         alt="">
    <span class="font-medium text-gray-700 break-all">${menu.name}</span>
`;
                    listContainer.appendChild(card);
                });
            })
            .catch(error => {
                console.error('類似メニューの取得に失敗しました。', error);
            });
        }, 300);
    });
});
