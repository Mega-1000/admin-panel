// types/globals.d.ts
interface Window {
    gtag: (...args: any[]) => void;
}


export const trackEvent = (action: string, category: string, label: string, value: number) => {
    // @ts-ignore
    if (typeof window !== 'undefined' && window.gtag) {
        // @ts-ignore
        window.gtag('event', action, {
            event_category: category,
            event_label: label,
            value: value,
        });
    }
};
