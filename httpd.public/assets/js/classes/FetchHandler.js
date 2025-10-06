export default class FetchHandler {
    constructor(debug = false) {
        this.debug = debug;
    }

    getFormFields(form) {
        var fields = [];
        // Parcourir tous les éléments du formulaire
        for (var i = 0; i < form.elements.length; i++) {
            var field = form.elements[i];
            // Ajouter l'élément au tableau si ce n'est pas un bouton
            if (field.type !== 'submit') {
                fields.push({
                    type: field.type,
                    name: field.name,
                    value: field.value,
                    disabled: field.disabled
                });
            }
        }
        return fields;
    }

    async sendRequest(dataToSend) {
        let headers = {};
        let body;
        let data; // Déclarez 'data' ici

        try {
            if (dataToSend instanceof FormData) {
                body = dataToSend;
            } else {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(dataToSend);
            }

            const response = await fetch('node.php', {
                method: 'POST',
                headers: headers,
                body: body
            });
            
            const rawResponse = await response.text();
            console.log('Réponse brute avant le parsing:', rawResponse);

            if (rawResponse.trim() === '') {
                throw new Error(`Réponse vide du serveur. Corps de la requête: ${JSON.stringify(body)}`);
            }

            try {
                data = JSON.parse(rawResponse); // Affectez à 'data' ici
            } catch (e) {
                console.error('Erreur lors de la conversion de la réponse en JSON:', rawResponse);
                throw new Error('Erreur lors du parsing de la réponse');
            }

            // Vérifiez 'data.success' ici
            if (data.success) {
                return data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.info(`Une erreur est survenue dans un envoi fetch. Corps de la requête: ${JSON.stringify(body)}`, error);
            throw error;
        }
    }

    async handleResponse(data, onSuccess, onError) {
        try {
            const response = await this.sendRequest(data);

            if (this.debug) {
                console.log(response);
            }

            if (response.success) {
                onSuccess(response);
            } else {
                onError(response);
            }
        } catch (error) {
            if (this.debug) {
                console.error("Erreur lors de l'envoi de la requête:", error);
            }
            onError(error);
        }
    }
}