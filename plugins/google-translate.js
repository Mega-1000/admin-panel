export default ({ app }) => {
    window.googleTranslateElementInit = () => {
        new window.google.translate.TranslateElement(
            { pageLanguage: 'en' }, // Set your default language here
            'google_translate_element'
        );
    };
};
