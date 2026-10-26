<x-app-layout>
    {{-- この画面（レイアウト内）で使うデータを定義 --}}
    @php
        $slots = [
            ['id' => 'slot-main', 'label' => 'メイン', 'type' => 'window.MENU_TYPES.MAIN'],
            ['id' => 'slot-sub-a', 'label' => '副菜A', 'type' => 'window.MENU_TYPES.SIDE_A'],
            ['id' => 'slot-sub-b', 'label' => '副菜B', 'type' => 'window.MENU_TYPES.SIDE_B'],
        ];
    @endphp

    {{-- メニュー登録件数が足りない時のメッセージ --}}
    @if (!$isReady)
        <div class="absolute top-0 md:top-[30%] left-[50%] translate-x-[-50%] z-20 w-full max-w-4xl mb-6 bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 rounded shadow-md" role="alert">
            <p class="mb-4 font-bold text-base md:text-lg flex gap-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-orange-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                スロットを回すための登録メニュー件数が足りません。
            </p>
            <p class="text-sm md:text-base">各料理タイプを3つ以上登録してください。</p>
            <div class="flex gap-4 mt-2 text-sm font-semibold">
                <span>メイン：{{ $counts['main'] }}/3</span>
                <span>副菜A：{{ $counts['side_a'] }}/3</span>
                <span>副菜B：{{ $counts['side_b'] }}/3</span>
            </div>
            <a href="{{ route('menus.index') }}" class="text-sm md:text-base mt-4 inline-flex items-center gap-2 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                </svg>
                メニュー管理画面で登録する
            </a>
        </div>
    @endif

    <x-flash-message />

    <div class="relative z-10 w-[98%] md:w-[80%] mx-auto bg-[#fee5a5] rounded-[2em] shadow-2xl p-8 md:p-10 text-center flex flex-col gap-10 justify-end">
        {{-- メインの黄色いカード --}}
        {{-- スロットコンテナ --}}
        <div class="flex justify-around items-center gap-[32px] flex-col md:flex-row gap-[24px]">
            @foreach ($slots as $slot )
                <x-slot-panel
                    :id="$slot['id']"
                    :label="$slot['label']"
                    :type="$slot['type']"
                    :isReady="$isReady"
                />
            @endforeach
        </div>
        <div id="slot-result-display"
            class="hidden mt-3 md:mt-6 p-4 bg-white/50 backdrop-blur-sm rounded-2xl border-2 border-dashed border-orange-300 flex items-center justify-center">
            <div class="text-orange-900 font-bold text-lg md:text-xl flex flex-col md:flex-row items-center gap-3">
                <span class="md:text-sm text-xs text-orange-600 font-normal">本日の献立：</span>
                <span id="display-main" class="text-sm md:text-base"></span>
                <span class="text-orange-300 hidden md:inline-block">/</span>
                <span id="display-sub-a" class="text-sm md:text-base"></span>
                <span class="text-orange-300 hidden md:inline-block">/</span>
                <span id="display-sub-b" class="text-sm md:text-base"></span>
            </div>
        </div>

        {{-- ナビゲーション --}}
        <x-sub-navigation current="slot" />
    </div>
    @push('scripts')
        {{-- JavaScript側にPHPのEnum値を定数として渡す！ --}}
        <script>
            window.MENU_TYPES = {
                MAIN: {{ \App\Enums\MenuType::Main->value }},
                SIDE_A: {{ \App\Enums\MenuType::SideA->value }},
                SIDE_B: {{ \App\Enums\MenuType::SideB->value }}
            };
        </script>
        @vite('resources/js/slot.js')
    @endpush
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(window.initSlot) {
            window.initSlot(@json($menus));
        }
    });
</script>
