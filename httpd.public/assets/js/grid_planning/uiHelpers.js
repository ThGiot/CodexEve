export function preselectThursday() {
    const filterDayElement = document.getElementById('filterDay');
    if (filterDayElement) {
        Array.from(filterDayElement.options).forEach(option => {
            if (option.value === 'Jeu') {
                option.selected = true;
            }
        });
    }
}
