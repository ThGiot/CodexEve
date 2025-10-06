export function updateHistory(newParams, currentPageParams, historyStack, forwardStack) {
    const { page, extraParams, isGoingBack } = newParams;

    if (!isGoingBack) {
        forwardStack.length = 0; // Réinitialise le forwardStack
        historyStack.push(currentPageParams);
        window.history.pushState({ ...currentPageParams, direction: 'forward' }, null);
    } else {
        window.history.pushState({ ...currentPageParams, direction: 'backward' }, null);
    }
}
