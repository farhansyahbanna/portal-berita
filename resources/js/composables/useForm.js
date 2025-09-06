import { ref, reactive, computed } from 'vue';
import { useNotifications } from './useNotifications';

export function useForm(initialValues = {}, validationRules = {}) {
    const form = reactive({ ...initialValues });
    const errors = reactive({});
    const isSubmitting = ref(false);
    const { showNotification } = useNotifications();

    const setError = (field, message) => {
        errors[field] = message;
    };

    const clearErrors = () => {
        Object.keys(errors).forEach(key => {
            errors[key] = '';
        });
    };

    const validateField = (field, value) => {
        if (!validationRules[field]) return true;
        
        const rules = validationRules[field];
        errors[field] = '';
        
        for (const rule of rules) {
            const isValid = rule.validator(value, form);
            if (!isValid) {
                errors[field] = rule.message;
                return false;
            }
        }
        
        return true;
    };

    const validateForm = () => {
        clearErrors();
        let isValid = true;
        
        Object.keys(validationRules).forEach(field => {
            if (!validateField(field, form[field])) {
                isValid = false;
            }
        });
        
        return isValid;
    };

    const resetForm = () => {
        Object.keys(initialValues).forEach(key => {
            form[key] = initialValues[key];
        });
        clearErrors();
    };

    const handleSubmit = async (submitFn) => {
        if (!validateForm()) {
            showNotification('Please fix the form errors', 'error');
            return false;
        }

        isSubmitting.value = true;
        
        try {
            await submitFn(form);
            return true;
        } catch (error) {
            if (error.response?.data?.errors) {
                Object.keys(error.response.data.errors).forEach(key => {
                    setError(key, error.response.data.errors[key][0]);
                });
            }
            showNotification(error.response?.data?.message || 'An error occurred', 'error');
            return false;
        } finally {
            isSubmitting.value = false;
        }
    };

    return {
        form,
        errors,
        isSubmitting,
        setError,
        clearErrors,
        validateField,
        validateForm,
        resetForm,
        handleSubmit
    };
}