export default class Log
{
    constructor(title) {
        this.title = title;
    }

    info(message) {
        if (import.meta.env.VITE_DEBUG_CONSOLE_LOGS_ENABLE !== false) {
            console.info(`[%c${this.title}%c] %c${message}`, 'color:  #881391', 'color: inherit', 'color:inherit; font-weight: semibold');
        }
    }

    error(message) {
        if (import.meta.env.VITE_DEBUG_CONSOLE_LOGS_ENABLE !== false) {
            console.error(`[%c${this.title}%c] %c${message}`, 'color:  #881391', 'color: #000', 'color:#212121; font-weight: semibold');
        }
    }
}