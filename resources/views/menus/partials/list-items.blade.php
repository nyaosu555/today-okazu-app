<div class="flex flex-wrap -mx-3 justify-start text-left">
    @foreach ($menus as $menu)
        <div class="w-full sm:w-1/2 xl:w-1/3 px-3 pb-6 flex">
            <div class="flex flex-col flex-grow w-full bg-[#FDF2C4] rounded-2xl shadow-sm border border-orange-100 overflow-hidden relative group hover:shadow-md transition duration-200">

                <div class="w-full aspect-[16/10] bg-white overflow-hidden relative border-b border-orange-900/5">
    @if($menu->image_path)
        <img src="{{ str_starts_with($menu->image_path, 'http') ? $menu->image_path : asset('storage/' . $menu->image_path) }}"
     onerror="this.onerror=null; this.src='{{ asset('images/no_image.png') }}';"
     class="w-full h-full object-cover group-hover:scale-102 transition duration-300">
    @else
        <img src="{{ asset('images/no_image.png') }}" class="w-full h-full object-cover">
    @endif
</div>

                <div class="p-4 flex flex-col flex-grow justify-between gap-3">

                    <div class="space-y-1.5">
                        <h3 class="text-orange-800 font-bold text-[18px] leading-[1.4] break-all">
                            {{ $menu->name }}
                        </h3>

                        {{-- ジャンルに応じた動的バッジカラー（ピンク・オレンジ・黄色） --}}
                        @if($menu->type_id == \App\Enums\MenuType::Main->value)
                            <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-red-50 text-red-800">
                                {{ $menu->type->name ?? 'メイン' }}
                            </span>
                        @elseif($menu->type_id == \App\Enums\MenuType::SideA->value)
                            <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-orange-100 text-orange-800">
                                {{ $menu->type->name ?? '副菜A' }}
                            </span>
                        @elseif($menu->type_id == \App\Enums\MenuType::SideB->value)
                            <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-yellow-100 text-yellow-800">
                                {{ $menu->type->name ?? '副菜B' }}
                            </span>
                        @else
                            <span class="inline-block text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600">
                                未分類
                            </span>
                        @endif

                        {{-- 管理者の場合のみ投稿者名を表示 --}}
                        @if (auth()->user()->role === 'admin')
                            <p class="mt-1 text-gray-500 text-xs">投稿者： {{ $menu->user->name }}</p>
                        @endif
                    </div>

                    {{-- 下部アクションエリア：編集ボタンと削除フォームを横並びに配置 --}}
                    <div class="flex justify-end items-center gap-1 border-t border-orange-900/10 pt-2 mt-auto">

                        {{-- 編集ボタンの制御（本人または管理者のみクリック可能） --}}
                        @if ($menu->user_id === auth()->id() || auth()->user()->role === 'admin')
                            <a href="{{ route('menus.edit', $menu) }}" class="p-2 text-gray-800 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition" title="編集">
                                <svg width="20" height="20" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <rect width="96" height="96" fill="url(#pattern0_1417_3)"/>
                                    <defs>
                                    <pattern id="pattern0_1417_3" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_1417_3" transform="scale(0.0104167)"/>
                                    </pattern>
                                    <image id="image0_1417_3" width="96" height="96" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAACVklEQVR4nO3cP2sUQRzG8W8lCooBLWxstbYWjIW9jaYQ34A2lpZOo1hHLUQsUvguRET0JeQ1BJv4Bws7ZXUPTt1N9nZm7zdmvx+Y7jaXe56Z29kJBCRJkiRJy7aAD8AXYBd4CBz/4xWazAPgR8d4B5yc7m3VSD3hL0azKk79eqXWHr4lVBC+JVQQviVUEL4lVBC+JWS6X6gAS8hgCRWwhCN0P1g8MXtsEVzCozG/gCj2ddQc4InfszqNuCa3gOYUdfbSUiDrLuE9M5c6QllnCTeYsXRAMOsoofl7wmylAQE9nvDGbPgMG1OUYPisNkqWYPiMGyVKMHzyRk4Jhk+ZMWZ3tOo1R0oqGP7YlTBbaYLwLaGC8C2hgvAtoYLwLaGC8C2hgvCb4T4fww+RDD9OMvw4yfDjJMOPkww/TjL8OMnw4yTDj5MMP04y/DiGH8jwAxl+IMMPZPiBDD+Q4Qcy/ECGH8jwgx0DLgA3gR3g0xrDT9EfvkYbwAvDjzdlCSn6w/0vK+Gz4cd65Q031m1nfqxLzvxYZ4HvzvwybgGXR157HrgOvAT2DX+cbeBrRgkLpwduUQ3/L6/bYEqUwCElGH6HvaWASpSw0XNsYfgdznQE9Q24mvlzd9ztDHOl56sidyVsOfOHuXPA93XOSrjozB/mySE7lrEr4YQzf5g3A7aNJe4J6vFx4IOTJUx0nLDK0YElFLY54vzGEgq6O/IQzRIKebpi8HvtscV2e4CnTG97gt5v/1Xvc+AecA04l/tm+tduuw191j6QbbY3ZkmSJEmSJEn0+wkGJTvfGbw62AAAAABJRU5ErkJggg=="/>
                                    </defs>
                                </svg>
                            </a>
                        @else
                            <span class="p-2 text-gray-400 opacity-40 cursor-not-allowed" title="登録者本人か管理者権限が必要です">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </span>
                        @endif

                        {{-- 削除ボタンのフォーム --}}
                        <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="delete-menu-form flex items-center">
                            @csrf
                            @method('DELETE')

                            @if ($menu->user_id === auth()->id() || auth()->user()->role === 'admin')
                                <button type="button" class="p-2 text-gray-800 hover:text-red-500 hover:bg-red-50 rounded-lg transition delete-individual-btn cursor-pointer" data-name="{{ $menu->name }}" title="削除">
                                    <svg width="20" height="20" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <rect width="96" height="96" fill="url(#pattern0_1417_2)"/>
                                        <defs>
                                        <pattern id="pattern0_1417_2" patternContentUnits="objectBoundingBox" width="1" height="1">
                                        <use xlink:href="#image0_1417_2" transform="scale(0.0104167)"/>
                                        </pattern>
                                        <image id="image0_1417_2" width="96" height="96" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAABeUlEQVR4nO3XTUoDURBF4Zq4F3WVDgV/0OXoNuJGNMMIVwI9chLEbk69fueDmqfvSUhSJUmSJEm93FVV/nn39EPMPH6MwI8fI/DjZ88RMvnhMvnhMvnhMvnhMvnhMvnhMvnhMvlJkiRJEo7+Y5TZ/3jRA8UA+7726IFigH1fe/RAMcC+rz16oBhg3QfKxoMZ4AID/BE9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgABQ9UAxgANTXCqOl6X3WAD4aDJWN7lADeG4wVDa6hxrAbVV9NxgrK9+pqq5rEK8NBsvKd/5kD+Oqqt4bjJaV7m15pqGcX/DL8tHNoHda3vnDjf/7O+Fp+QVxbDBqLtxxea2PVXVDjydJkiSpducHthOH9YSFxRsAAAAASUVORK5CYII="/>
                                        </defs>
                                    </svg>
                                </button>
                            @else
                                <button disabled type="button" class="p-2 text-gray-400 opacity-40 delete-individual-btn cursor-not-allowed" title="登録者本人か管理者権限が必要です">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            @endif
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>
