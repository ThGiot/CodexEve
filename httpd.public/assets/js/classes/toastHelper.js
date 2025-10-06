// toastHelper.js
import Toast from './Toast.js';

export function showToast(response, isError = false) {
    const message = isError ? "Une erreur s'est produite " + (response.message || '') : response.message;
    const options = {
        position: 'top-right',
        actionButton: false,
        closeButton: true,
        backgroundColor: isError ? 'rgb(255, 99, 71)' : '-1', // Teinte de rouge attrayante
        delay: isError ? 4000 : 2000
    };

    new Toast(message, options);
}
