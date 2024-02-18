import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import pl from './locales/pl.json';

export default createI18n({
    locale: import.meta.env.VITE_DEFAULT_LOCALE,
    fallbackLocale: import.meta.env.VITE_FALLBACK_LOCALE,
    globalInjection: true,
    legacy: false,
    messages: {
        en,
        pl,
    },
});