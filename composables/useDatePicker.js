import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

export const useDatePicker = (el, options) => {
    const flatPickrInstance = flatpickr(el, options);

    return {
        flatPickrInstance,
    };
};
