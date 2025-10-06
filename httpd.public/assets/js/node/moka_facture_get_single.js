export function moka_facture_get_single(param) {
    const { factureId } = param;
    fetch('node.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ node: 'action', action: 'facture_get_single', facture_id : factureId})
    })
    .then(response => {
        console.log("Response headers and status:", response);
        if (!response.ok) {
            throw new Error('Réponse réseau non OK');
        }
        return response.blob();  // convertit le ReadableStream en Blob
    })
    .then(blob => {
        console.log('Received blob:', blob); // Pour débogage
        if (blob.size === 0) {
            throw new Error('Blob is empty. No data received from server.');
        }
        if (blob.type.includes("html")) {
            // Convertir le blob HTML en texte pour voir ce qu'il contient
            return blob.text().then(text => {
                console.log("HTML content from blob:", text);
                throw new Error('Received HTML instead of expected PDF.');
            });
        }
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'facture_'+factureId+'.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error('Erreur de téléchargement:', error);
    });
}
