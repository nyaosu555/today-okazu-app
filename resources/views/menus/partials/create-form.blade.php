<form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data" id="menu-form" class="space-y-6 text-left">
    @csrf

    {{-- メニュー名 --}}
    <div class="flex flex-col items-start gap-2 mt-4">
        <label for="menu_name" class="text-[#DA5019] font-bold">メニュー名※</label>
        <input id="menu_name" type="text" name="menu_name" value="{{ old('menu_name') }}" class="w-full md:w-[70%] border border-[#DA5019] focus:outline-none focus:ring-2 focus:ring-[#D97706] focus:border-none rounded-md shadow-sm p-2">
        @error('menu_name')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror

        <div id="similar-menus-container" class="w-full md:w-[70%] mt-2 hidden">
            <p class="text-sm font-bold text-amber-600 mb-2 flex items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect width="96" height="96" fill="url(#pattern0_1418_5)"/>
                    <defs>
                    <pattern id="pattern0_1418_5" patternContentUnits="objectBoundingBox" width="1" height="1">
                    <use xlink:href="#image0_1418_5" transform="scale(0.0104167)"/>
                    </pattern>
                    <image id="image0_1418_5" width="96" height="96" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAAAsTAAALEwEAmpwYAAAEcElEQVR4nO2cy44bRRSGi2W4ixBNn2OTkcL4lMdAQIIHQPACXMMlXBY8BAh2IeRCuASeAfEACJJxHRsTGGUFQmKfcCfZsQABQyI1KnviZDHGsftyqqrPJ/3Lma7z/139t+V2G6MoiqIoiqIoiqIoiqIogTIiuJMJXnOE3zgLv3sx4deO4FXu4m7p9SVNn1qPOMKLbDHfSY7wAtvWw9LrTJJ+Bx9wFv6aZf40BAt/O5s9JL3epDjbbu9yFs/NM/9qCHjO/430upPBWTh0veZfsxMOSa87CYZrd93tLyuLBsAW/uEukvT6o4ctfLq4+dsi7EuvP2r6HXh8afO3NSB4THqOKDnbbu9ii+eLBsCEP/b3r9wkPU90MMHhwuZPPx/Am9LzRMVGr722XPHO2gWwNbRopeeKBrbwWWnmT3cBOum5osB1syfKNv+KfKlLzxd+8RJ+X1UAbOEnLeT/gQnfqs78aR8clp4z2OJl/+m1+gC0kHfCWTxVuflayDszsK2n6jL/qrInpecOgk8ehBurLd7ZhTzq7bnZNB1HeKR+86eXoiOmybj1VqeW4p0lgq0Ngq5pKq7G4p25CyywaSLOtp6WNv+K/E2AaVrxOsIfpI2/Zhf83KhCZovHljXLzP/fywZx1DSBosVrqgqAYMt1snWTOs7i6SKXCzOHYpcjGJiUcYQHil6vzRwK9wHhAZMiZRWvqTqAVAvZWTxe1ByuIYBJCHjcpIR/QKqsT7xm3rFKOEZyhcwWhqUYY2sKYKIzuTE3mNgZdPDZEk3J5x2vzGMx4TMmZjbt7luchV9iDcARXji1dsetJlaY4ESpZ6SteQdMHup628TIYC3rOYJ/4w8AL7Ft32eaXLwsGMA4BItfRlXIrgvPVWEECwXg5W8mTFOLlwMIYPwDwH37bjOhwwTvVGUCCwYwFsEJ08Ti5UAC8IU8oNZ+EyqO4PNKz0ArvANCLmS2cLDq4TmAACaC501oxcsEvzYlABdaITsL79Vz5mEwchbeNSEwXF+5p+ri5QAVTCHXUbwcrr4SLeQ+ZS8EYEIuKziYfPFywBq/PkeikNni+9LDcyDyNyG1mt/U4uVZARBe2ui07q/FfF86jmAkPTQ3tZC503pJclAzb32iOyF7sVLz/fejTPCbBoAzAsCLo9XV2ysLgAlPSg7IgQcwFuHJSszvd1fuDaF4TegBWLxceiH7cmHCLwIYLo8gAK/NUguZu/hyAEPlEQWQ+5uVZIqXIwygtEJ2hB9KD8MRBrAdwgeFzPcPJE0eTJIfhuPUZf/G3wIB4GYAQ+SRa3Mp8x3howEsPk9B/qXji5/9hB9LL5wTkSP8aIkA4DvphXMygm+X2QECr5DBVHV+4QD8A0gBLDxPRGeWCeCNABaepyBn4fWFAxj19mSO8A/pxXPk8h6e7u4Fswz+R2rSA3DkKvwlzfjRE8I/pQfhyOTffT0geMWUwXA9Wx3/0t3fmmoY+Ux5bya378e4B3tLMV9RFEVRFEVRFEVRFEUxqfAfeqN4q0FPKqgAAAAASUVORK5CYII="/>
                    </defs>
                </svg>
                もしかして、すでに同じメニューが登録されていませんか？
            </p>
            <div id="similar-menus-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-amber-50/50 p-3 rounded-lg border border-amber-200">
            </div>
            <template id="similar-menu-template">
                <div class="flex items-center gap-2 p-2 bg-white rounded border border-gray-200 shadow-sm text-sm">
                    <img src=""
                        class="w-10 h-10 object-cover rounded-md border border-gray-100 shrink-0"
                        alt=""
                    >
                    <span class="font-medium text-gray-700 break-all"></span>
                </div>
            </template>
        </div>
    </div>

    {{-- ジャンル --}}
    <div class="flex flex-col items-start gap-2 pt-2">
        <label class="block text-[#DA5019] font-bold">ジャンル※</label>
        <div class="flex flex-wrap gap-4 sm:gap-6">
            @foreach ($types as $type)
                <label for="type_{{ $type->id }}" class="inline-flex items-center cursor-pointer bg-orange-50/50 hover:bg-orange-50 px-3 py-1.5 rounded-lg border border-transparent transition select-none">
                    <input type="radio" name="type_id" value="{{ $type->id }}"
                        {{ old('type_id') == $type->id ? 'checked' : '' }}
                        class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500"
                        id="type_{{ $type->id }}"
                    >
                    <span class="ml-2 text-[#DA5019] font-medium text-sm sm:text-base">{{ $type->name }}</span>
                </label>
            @endforeach
        </div>
        @error('type_id')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror
    </div>

    {{-- 写真（ファイル選択） --}}
    <div class="flex flex-col items-start gap-2 pt-2">
        <label class="block text-[#DA5019] font-bold" for="imageInput">写真</label>
        {{-- 本物のボタンは隠す --}}
        <input type="file" name="image_path" id="imageInput" onchange="previewImage(this)" class="hidden">

        <input type="hidden" name="current_image_status" id="currentImageStatus" value="{{ old('current_image_status', 'no_change') }}">
        <input type="hidden" name="buffered_image_data" id="bufferedImageData" value="{{ old('buffered_image_data') }}">
        {{-- ボタンとファイル名の並び：スマホでは縦並びにして溢れを防ぎ、sm以上で横並びに --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
            {{-- 💡 label側のonclickは、JS側のキャンセル処理と干渉させないためにconsole.logのみにスッキリ修正 --}}
            <label for="imageInput"
                   class="cursor-pointer bg-[#DA5019] text-sm md:text-base text-white px-5 py-2.5 rounded-lg font-bold hover:bg-[#b84315] transition text-center shadow-sm shrink-0">
                画像を選択
            </label>
            {{-- ファイル名を表示 --}}
            <span id="fileNameDisplay" class="text-gray-500 text-sm italic break-all">ファイルが選択されていません。</span>
        </div>

        {{-- バリデーションエラーを表示 --}}
        <p id="jsImageError" class="text-red-500 text-sm mt-1" style="white-space: pre-line; text-align: left;"></p>
        @error('image_path')
            <p class="text-red-500 text-sm mt-1 laravel-image-error" style="white-space: pre-line; text-align: left;">{{ $message }}</p>
        @enderror
    </div>

    {{-- 画像のプレビュー表示エリア --}}
    <div class="w-full max-w-[400px] aspect-[16/10] relative border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 overflow-hidden">
        {{-- 初期状態では隠す --}}
        <img src="" id="imagePreview" alt="メニュー画像プレビュー" class="w-full h-full object-contain hidden">

        {{-- 画像がない時に表示するエリア --}}
        <div id="placeholder" class="absolute inset-0 flex flex-col items-center justify-center p-4">
            <span class="text-gray-400 text-xs sm:text-sm text-center">画像が選択されていません。</span>
        </div>

        {{-- プレビュー画像削除ボタン --}}
        <button type="button" id="deleteImageBtn" onclick="clearImage()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition shadow hidden">✕</button>
    </div>

    {{-- 送信ボタン --}}
    <div class="flex justify-center">
        <button type="submit" class="w-full text-sm md:text-base md:w-auto bg-[#DA5019] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#b84315] transition shadow-md cursor-pointer text-center" id="save-menu-btn">
            メニューを追加
        </button>
    </div>
</form>
