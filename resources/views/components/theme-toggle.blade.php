<div
    x-data="{ darkMode: $store.darkMode.on }"
    x-effect="$store.darkMode.on = darkMode"
    x-switch:group
    x-cloak
    class="flex items-center justify-center"
>
    <div class="flex items-center space-x-2">
        <button
            x-switch
            x-model="darkMode"
            :class="$switch.isChecked ? 'bg-gray-900' : 'bg-gray-300'"
            class="relative inline-flex w-9 rounded-full py-[0.1rem] transition"
            aria-label="Toggle Dark Mode"
            >
            <span
                :class="$switch.isChecked ? 'bg-gray-500 translate-x-[1.4rem]' : 'bg-white translate-x-[0.1rem]'"
                class="h-3 w-3 rounded-full transition shadow-md"
            ></span>
        </button>
        <svg x-show="darkMode" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="12" viewBox="0 0 384 512"><path d="M223.5 48C108.9 48 16 141.1 16 256s92.9 208 207.5 208c56.2 0 107.2-22.4 144.6-58.8c-10.7 1.9-21.7 2.8-32.9 2.8c-105.8 0-191.5-86-191.5-192c0-71.7 39.3-134.3 97.4-167.3c-5.8-.5-11.7-.7-17.6-.7zM0 256C0 132.3 100 32 223.5 32c6.4 0 12.7 .3 19 .8c7 .6 12.8 5.7 14.3 12.5s-1.6 13.9-7.7 17.3c-53.3 30.2-89.3 87.6-89.3 153.3c0 97.2 78.6 176 175.5 176c10.3 0 20.4-.9 30.1-2.6c6.9-1.2 13.8 2.2 17 8.5s1.9 13.8-3.1 18.7C339 455.8 284 480 223.5 480C100 480 0 379.7 0 256z"/></svg>
        <svg x-show="!darkMode" class="text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="16" viewBox="0 0 512 512"><path d="M165.7 2.8c-4.5-3.1-10.2-3.7-15.2-1.6s-8.6 6.6-9.6 11.9L121 121 13.1 140.8c-5.3 1-9.8 4.6-11.9 9.6s-1.5 10.7 1.6 15.2L65.1 256 2.8 346.3c-3.1 4.5-3.7 10.2-1.6 15.2s6.6 8.6 11.9 9.6L121 391l19.8 107.9c1 5.3 4.6 9.8 9.6 11.9l3.1-7.4-3.1 7.4c5 2.1 10.7 1.5 15.2-1.6L256 446.9l90.3 62.3c4.5 3.1 10.2 3.7 15.2 1.6s8.6-6.6 9.6-11.9L391 391l107.9-19.8c5.3-1 9.8-4.6 11.9-9.6s1.5-10.7-1.6-15.2L446.9 256l62.3-90.3c3.1-4.5 3.7-10.2 1.6-15.2l-7.4 3.1 7.4-3.1c-2.1-5-6.6-8.6-11.9-9.6L391 121 371.1 13.1c-1-5.3-4.6-9.8-9.6-11.9s-10.7-1.5-15.2 1.6L256 65.1 165.7 2.8zm94.9 78.6L355.4 16l20.8 113.3c.6 3.3 3.2 5.8 6.4 6.4L496 156.6l-65.4 94.9c-1.9 2.7-1.9 6.3 0 9.1L496 355.4 382.7 376.2c-3.3 .6-5.8 3.2-6.4 6.4L355.4 496l-94.9-65.4c-2.7-1.9-6.3-1.9-9.1 0L156.6 496 135.8 382.7c-.6-3.3-3.2-5.8-6.4-6.4L16 355.4l65.4-94.9c1.9-2.7 1.9-6.3 0-9.1L16 156.6l113.3-20.8c3.3-.6 5.8-3.2 6.4-6.4L156.6 16l94.9 65.4c2.7 1.9 6.3 1.9 9.1 0zM256 368a112 112 0 1 0 0-224 112 112 0 1 0 0 224zM160 256a96 96 0 1 1 192 0 96 96 0 1 1 -192 0z"/></svg>
    </div>
</div>