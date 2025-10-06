import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
export function userSelfModif(params){
    const { formId } = params;
    const fetchHandler = new FetchHandler(true);

    const data = {
        node : 'action',
        action : 'user_self_modif',
        module_personnal : true,
        email: document.getElementById('email').value,
        telephone: document.getElementById('telephone').value
    };
    
    fetchHandler.sendRequest(data)
        .then(response => {
            console.log(response);
            if(response.success == true){
                const message = "Modification enregistrée";
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,     
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 2000
                };
                const myToast = new Toast(message, options);

            }else{
                const message = "Une erreur s'est produite "+response.message;
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,     
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 4000
                };
                const myToast = new Toast(message, options);
            }
        })
        .catch(error => {
            console.error("Erreur lors de l'envoi de la requête:", error);
        });


}