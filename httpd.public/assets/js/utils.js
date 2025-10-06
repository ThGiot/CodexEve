export function fetchContent(page, extraParams) {
    const params = { node: 'content', page, ...extraParams };
    return fetch('node.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(params),
    }).then(response => {
        if (!response.ok) {
            return response.text().then(err => {
                throw new Error(err);
            });
        }
        return response.text();
    });
}

export function reloadScripts(scriptPath) {
    const existingScript = document.querySelector(`script[src="${scriptPath}"]`);
    if (existingScript) existingScript.remove();
    const script = document.createElement('script');
    script.src = scriptPath;
    script.type = 'module';
    document.body.appendChild(script);
}
