export function initFlatpickr() {
    const pickers = document.querySelectorAll('.datetimepicker');
    if (!pickers.length) return;

    pickers.forEach(picker => {
        try {
            const options = JSON.parse(picker.dataset.options);
            flatpickr(picker, options);
        } catch (error) {
            console.error("Erreur d'initialisation de Flatpickr :", error);
        }
    });
}
