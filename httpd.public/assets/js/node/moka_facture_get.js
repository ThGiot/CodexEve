export function moka_facture_get(param) {
    const {fichier} = param;
    fetch('node.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ node: 'action', action: 'get_pdf_generate', fichier: fichier})
    })
    .then(response => response.blob(console.log(response)))
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        console.log(blob);
        a.download = fichier; // Mettez ici le nom souhaité pour le fichier téléchargé
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
        console.log(url);
    })
    .catch(error => console.error('Erreur de téléchargement:', error));
  
}
