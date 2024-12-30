<div
    x-data="{
        messages: [],
    }"
    @notify.window="({ detail }) => {
        messages.push(detail)

        setTimeout(() => {
           messages.shift()
        }, 3000)
    }"
    x-cloak>
    <div
        class="fixed grid place-content-center top-[3.1rem] w-full z-50"
        x-show="messages.length" x-transition.duration.250ms>
        <template x-for="(message, index) in messages" :key="index">
            <span
                class="bg-gray-100 dark:bg-gray-900 mt-2 px-7 py-2 rounded-md shadow-2xl text-center text-gray-800 dark:text-gray-200 select-none"
                x-text="message">
            </span>
        </template>
    </div>
</div>