const Trans = {
    get supportedLocales() {
        return import.meta.env.SUPPORTED_LOCALES.split(',');
    }
};

export default Trans;