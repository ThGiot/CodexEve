import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
export function saveParam(params){
    const { clientId, paramId, moduleId } = params;


    const fetchHandler = new FetchHandler(true);

    const data = {
        node : 'action',
        action : 'module_param_save',
        client_id : clientId,
        param_id : paramId,
        module_id : moduleId,
        param_value : document.getElementById('param_'+paramId).value
    };
    
    fetchHandler.sendRequest(data)
        .then(response => {
            console.log(response);
            if(response.success == true){
                if(response.statut == 101){
                    var errorDivOld = document.getElementById('oldPassError');
                    passOld.setCustomValidity('');
                   // passOld.setCustomValidity('Ancien mot de passe Incorrect');
                    errorDivOld.style.display = 'block';
                    passOld.reportValidity();
                    return;
                }
                const message = response.message;
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,     
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 2000
                };
                const myToast = new Toast(message, options);
                document.getElementById('closeModal').click();


            }else{
                const message = "Une erreur s'est produite :"+response.message;
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
            const message = error;
                const options = {
                position: 'top-right', 
                actionButton: false,    
                closeButton: true,     
                backgroundColor: '#FF4500', // Couleur de fond que vous souhaitez utiliser
                delay: 4000
                };
                const myToast = new Toast(message, options);
    });
    

}