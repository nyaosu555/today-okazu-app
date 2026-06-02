import axios from "axios";
import { gsap } from "gsap";

let allMenus = [];
// 各スロットのアニメーションを保存するオブジェクト
const activeTimelines = {
    main: null,
    sub_a: null,
    sub_b: null,
}

// 当選したメニューを保持するオブジェクト
let selectedResults = {
    [window.MENU_TYPES.MAIN]: null,     // 主菜
    [window.MENU_TYPES.SIDE_A]: null,   // 副菜A
    [window.MENU_TYPES.SIDE_B]: null,   // 副菜B
};

const saveBtn = document.getElementById('save-button');

const resultMain = document.getElementById('result-main');
const resultSubA = document.getElementById('result-sub-a');
const resultSubB = document.getElementById('result-sub-b');

window.toggleSlot = function(type, isRolling) {
    const idMap = {
        [window.MENU_TYPES.MAIN]: 'slot-main',
        [window.MENU_TYPES.SIDE_A]: 'slot-sub-a',
        [window.MENU_TYPES.SIDE_B]: 'slot-sub-b'
    };

    const ul = document.getElementById(idMap[type]);
    if(!ul) return;

    const liElements = ul.querySelectorAll('.slot-li');

    if (isRolling) {
        selectedResults[type] = null;
        updateSaveButtonState();

        // 【追加】回転開始時に、該当するスロットの表示をクリアする
        const resEl = type === window.MENU_TYPES.MAIN ? resultMain : (type === window.MENU_TYPES.SIDE_A ? resultSubA : resultSubB);
        if (resEl) resEl.textContent = '...';

        const items = allMenus.filter(m => m.type_id === type);
        const h = 75;
        const totalHeight = items.length * h;

        // 初回のみタイムラインを作成、2回目以降は既存のものを使う
        if (!activeTimelines[type]) {
            activeTimelines[type] = gsap.to(liElements, {
                y: `+=${totalHeight}%`, // 1周分の距離
                duration: 0.5,          // 速度
                ease: "none",
                repeat: -1,
            });
        }

        // 提示コードの応用：
        // timeScale(1) で速度を戻し、resume() で止まった所から再開
        activeTimelines[type].timeScale(1).resume();

    } else {
        if (activeTimelines[type]) {
            // アニメーションを一時停止
            activeTimelines[type].pause();

            // 現在の「中途半端な位置」から「キリの良い位置（中央）」へ吸着させる
            const items = allMenus.filter(m => m.type_id === type);
            const randomIndex = Math.floor(Math.random() * items.length);
            const offset = -12.5;

            // 当選したメニューのデータを保存
            const wonMenu = items[randomIndex];
            selectedResults[type] = wonMenu;
            updateSaveButtonState();

            gsap.to(liElements, {
                y: (i) => (-(i - 1 - randomIndex) * 75 + offset) + "%",
                duration: 1.5,
                ease: "back.out(1.2)",
                onComplete: () => {
                    isSelectionComplete();

                    // 各スロットの下（pタグ）の更新
                    const resEl = type === window.MENU_TYPES.MAIN ? resultMain : (type === window.MENU_TYPES.SIDE_A ? resultSubA : resultSubB);
                    if (resEl) resEl.textContent = wonMenu.name;

                    // 中央結果エリアの更新
                    const displayIdMap = {
                        [window.MENU_TYPES.MAIN]: 'display-main',
                        [window.MENU_TYPES.SIDE_A]: 'display-sub-a',
                        [window.MENU_TYPES.SIDE_B]: 'display-sub-b',
                    };

                    const displaySpan = document.getElementById(displayIdMap[type]);
                    if (displaySpan) {
                        displaySpan.textContent = wonMenu.name;
                    }

                    //【修正点】hiddenを削除して表示させる
                    const resultArea = document.getElementById('slot-result-display');
                    if (resultArea) {
                        resultArea.classList.remove('hidden');

                        // もし「パッ」と出るのが急すぎると感じたら、ここだけGSAPでふわっとさせてもOK
                        gsap.fromTo(resultArea, { opacity: 0 }, { opacity: 1, duration: 0.3 });
                    }
                }
            });
        }
    }

};

window.initSlot = function(menus) {

    allMenus = menus;
    // それぞれのスロット（ul）に要素を追加する
    setupSlot('slot-main', window.MENU_TYPES.MAIN);
    setupSlot('slot-sub-a', window.MENU_TYPES.SIDE_A);
    setupSlot('slot-sub-b', window.MENU_TYPES.SIDE_B);

    updateSaveButtonState();
};

function setupSlot(elementId, category) {
    const ul = document.getElementById(elementId);
    if (!ul) return;

    const items = allMenus.filter(menu => menu.type_id === category);
    if (items.length === 0) return;

    ul.innerHTML = '';

    // ループ用に、最後のメニューを先頭にも入れる
    const displayItems = (items.length >= 3)
    ? [items[items.length - 1], ...items, items[0]]
    : items;

    displayItems.forEach((item) => {
        const li = document.createElement('li');
        li.dataset.typeId = item.type_id; // data-type-id になる
        li.dataset.name = item.name;      // data-name になる
        // li.textContent = `${item.name} (ID:${item.type_id})`; // 画面上にも出す
        // 画像パスの組み立て
        const rawPath = item.image_path;
        let imgPath = 'images/no_image.png';

        if (rawPath) {
            // httpから始まっている（URLである）場合はそのまま使う
            if (rawPath.startsWith('http')) {
                imgPath = rawPath;
            } else {
                // これまで通り、ユーザーがアップロードしたローカルの画像の場合
                imgPath = `/storage/${rawPath}`;
            }
        }

        // 「上下を暗くするグラデーション」をセット
        li.style.backgroundImage = `url("${imgPath}"), url("/images/no_image.png")`;
        li.classList.add('slot-li');
        ul.appendChild(li);
    });

    // GSAPで初期位置を決定（これで上部がチラ見えする）
    const liElements = ul.querySelectorAll('.slot-li');

    // スロットの1枚の高さを変数にする（CSSと合わせる）
    const h = 75;

    // 中央に配置するためのオフセット
    const offset = -12.5;

    gsap.set(liElements, {
        y: (i) => {
            // 0番目が中央、1番目がその上、2番目がさらに上...と配置される
            return (-(i - 1) * h + offset) + "%";
        }
    });
}

function isSelectionComplete() {
    return selectedResults[window.MENU_TYPES.MAIN] && selectedResults[window.MENU_TYPES.SIDE_A] && selectedResults[window.MENU_TYPES.SIDE_B];
}

function updateSaveButtonState() {
    const saveBtn = document.getElementById('save-button');
    if (!saveBtn) return;

    // 3つすべてにデータが入っているかチェック
    const isComplete = selectedResults[window.MENU_TYPES.MAIN] && selectedResults[window.MENU_TYPES.SIDE_A] && selectedResults[window.MENU_TYPES.SIDE_B];

    if (isComplete) {
        // 押せる状態
        saveBtn.style.pointerEvents = 'auto';
        saveBtn.style.opacity = '1';
        saveBtn.classList.remove('cursor-not-allowed'); // カーソルを通常に
    } else {
        // 押せない状態
        saveBtn.style.pointerEvents = 'none';
        saveBtn.style.opacity = '0.3'; // 少し暗くして「無効」を表現
        saveBtn.classList.add('cursor-not-allowed'); // 禁止マークのカーソルに（Tailwind）
    }
}

if(saveBtn) {
    saveBtn.addEventListener('click', async(e) => {
        e.preventDefault();

        // 万が一、無効化をすり抜けて押された場合の最終ガード
        if(!isSelectionComplete()) {
            alert('すべてのスロットを止めて、献立を確定させてください。');
            return; // ここで処理を中断
        }

        // 送信データの作成
        console.log("保存処理を開始します...");
        const postData = {
            main_dish_id: selectedResults[window.MENU_TYPES.MAIN].id,
            sub_dish_a_id: selectedResults[window.MENU_TYPES.SIDE_A].id,
            sub_dish_b_id: selectedResults[window.MENU_TYPES.SIDE_B].id,
        };

        // 自作の確認モーダルを呼び出す
        window.dispatchEvent(new CustomEvent('confirm-delete', {
            detail: {
                message: '本日の献立を保存してもよろしいでしょうか？',
                btnText: '保存する',
                action: async() => {
                    try {
                        // LaravelのルートへPOST送信
                        const response = await axios.post('/meal-records', postData);

                        if (response.status === 200) {
                            saveBtn.style.pointerEvents = 'none';
                            saveBtn.style.opacity = '0.3';

                            const messageBox = document.getElementById('flash-message');
                            const flashIcon = document.getElementById('flash-icon'); // アイコン用のID
                            const flashText = document.getElementById('flash-text'); // テキスト用のID

                            if (messageBox && flashIcon && flashText) {
                                // 文字列だけを書き換える（アイコンは消さない）
                                flashText.textContent = '今日の献立を保存しました！';

                                // 成功時のアイコン(SVG)をセット
                                flashIcon.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 flex-shrink-0">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                `;

                                // クラスの差し替え
                                messageBox.classList.remove('bg-red-100', 'border-red-700', 'text-red-800');
                                messageBox.classList.add('bg-green-100', 'border-green-700', 'text-green-800');

                                // アニメーション開始
                                requestAnimationFrame(() => {
                                    messageBox.classList.remove('-translate-y-full', 'opacity-0');
                                    messageBox.classList.add('translate-y-0', 'opacity-100');
                                });

                                // 自動で消す（5秒〜7秒後）
                                setTimeout(() => {
                                    messageBox.classList.remove('opacity-100', 'translate-y-0');
                                    messageBox.classList.add('opacity-0', '-translate-y-full');
                                }, 7000);
                            }
                        }
                    } catch (error) {
                        console.error('保存エラー', error);

                        if(error.response && error.response.status === 409) {
                            // alert(error.response.data.message);
                            window.dispatchEvent(new CustomEvent('alert-message', {
                                detail: {message: error.response.data.message}
                            }));
                        } else if (error.response && error.response.status === 422) {
                            window.dispatchEvent(new CustomEvent('alert-message', {
                                detail: {message: '保存に失敗しました。内容を確認して、再度お試しください。'}
                            }));
                        } else {
                            // alert('保存に失敗しました。ログイン状態を確認してください。');
                            window.dispatchEvent(new CustomEvent('alert-message', {
                                detail: {message: '保存に失敗しました。ログイン状態を確認してください。'}
                            }));
                        }
                    }
                }
            }
        }));
    });
}

