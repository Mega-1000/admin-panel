interface Rules {
    [key: string]: {
        required?: boolean;
        minLength?: number;
        maxLength?: number;
        pattern?: RegExp;
        custom?: (value: any) => boolean;
    };
}

const validate = (rules: Rules, form: any) => {
    const errors: any = {};

    for (const key in rules) {
        const rule = rules[key];
        const value = form[key];

        if (rule.required && !value) {
            errors[key] = "To pole jest wymagane";
        }

        if (rule.minLength && value && value.length < rule.minLength) {
            errors[key] = `To pole musi mieć co najmniej ${rule.minLength} znaków`;
        }

        if (rule.maxLength && value && value.length > rule.maxLength) {
            errors[key] = `To pole może mieć maksymalnie ${rule.maxLength} znaków`;
        }

        if (rule.pattern && value && !rule.pattern.test(value)) {
            errors[key] = "To pole jest nieprawidłowe";
        }

        if (rule.custom && value && !rule.custom(value)) {
            errors[key] = "To pole jest nieprawidłowe";
        }
    }

    return Object.keys(errors).length === 0 ? null : errors;
}

export default validate;
