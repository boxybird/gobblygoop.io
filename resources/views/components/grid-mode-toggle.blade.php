<div
    x-data="{ gridMode: $store.gridMode.on }"
    x-effect="$store.gridMode.on = gridMode"
    x-switch:group
    x-cloak
    class="flex items-center justify-end"
>
    <div class="flex items-center space-x-2">
        <button
            x-switch
            x-model="gridMode"
            :class="$store.darkMode.on ? 'bg-gray-500' : 'bg-gray-300'"
            class="relative inline-flex w-9 rounded-full py-[0.1rem] transition"
            aria-label="Toggle Grid Mode"
            >
            <span
            :class="[
                $switch.isChecked ? 'translate-x-[1.4rem]' : 'translate-x-[0.1rem]',
                $store.darkMode.on ? 'bg-gray-900' : 'bg-white'
            ]"
            class="h-3 w-3 rounded-full transition shadow-md"
            ></span>
        </button>
        <svg 
            x-show="!$store.gridMode.on" 
            :class="$store.darkMode.on ? 'text-white' : 'text-gray-900'" 
            xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="20" viewBox="0 0 576 512"><path d="M106.3 124.3C151.4 82.4 212.4 48 288 48s136.6 34.4 181.7 76.3c44.9 41.7 75 91.7 89.1 125.6c1.6 3.9 1.6 8.4 0 12.3C544.7 296 514.6 346 469.7 387.7C424.6 429.6 363.6 464 288 464s-136.6-34.4-181.7-76.3C61.4 346 31.3 296 17.2 262.2c-1.6-3.9-1.6-8.4 0-12.3C31.3 216 61.4 166 106.3 124.3zM288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM192 256a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zm208 0a112 112 0 1 0 -224 0 112 112 0 1 0 224 0z"/>
        </svg>
        <svg 
            x-show="$store.gridMode.on" 
            :class="$store.darkMode.on ? 'text-white' : 'text-gray-900'" 
            xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="20" viewBox="0 0 640 512"><path d="M13 1.7C9.5-1 4.5-.4 1.7 3S-.4 11.5 3 14.3l624 496c3.5 2.7 8.5 2.2 11.2-1.3s2.2-8.5-1.3-11.2L13 1.7zM605.5 268.3c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-47.8 0-90.1 12.9-126.3 32.5l13.7 10.9C240.3 58.7 277.9 48 320 48c75.6 0 136.6 34.4 181.7 76.3c44.9 41.7 75 91.7 89.1 125.6c1.6 3.9 1.6 8.4 0 12.3C581.7 284 566 312.4 544 341.1l12.6 9.9c23-29.9 39.4-59.7 49-82.7zM83.5 161c-23 29.9-39.4 59.7-49 82.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 90.1-12.9 126.3-32.5l-13.7-10.9C399.7 453.3 362.1 464 320 464c-75.6 0-136.6-34.4-181.7-76.3C93.4 346 63.3 296 49.2 262.2c-1.6-3.9-1.6-8.4 0-12.3C58.3 228 74 199.6 96 170.9L83.5 161zM320 368c7.8 0 15.4-.8 22.7-2.3l-17.5-13.8c-1.7 .1-3.5 .1-5.2 .1c-47.2 0-86.4-34-94.5-78.9L208 259.4C209.8 319.7 259.3 368 320 368zm0-224c-7.8 0-15.4 .8-22.7 2.3l17.5 13.8c1.7-.1 3.5-.1 5.2-.1c47.2 0 86.4 34 94.5 78.9L432 252.6C430.2 192.3 380.7 144 320 144z"/>
        </svg>
    </div>
</div>