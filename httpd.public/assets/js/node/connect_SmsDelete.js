import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
export function connect_SmsDelete(params){
    const {smsId } = params;

    const fetchHandler = new FetchHandler(true);

    const data = {
        node : 'action',
        action : 'connect_sms_delete',
        sms_id: smsId
    };
    
    fetchHandler.sendRequest(data)
        .then(response => {
            console.log(response);
            if(response.success == true){
                refreshContent();
                const message = "SMS Supprimé";
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