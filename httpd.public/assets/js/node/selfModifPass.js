import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
export function selfModifPass(params){
    var passOld = document.getElementById('old_password');
    var passNew1 = document.getElementById('new_password');
    var passNew2 = document.getElementById('new_password2');

    // Réinitialise le message d'erreur personnalisé.
    var errorDiv = document.getElementById('samePass');
    passNew2.setCustomValidity('');

    // Compare les deux mots de passe.
    if (passNew1.value !== passNew2.value) {
        // Si les mots de passe sont différents, définit un message d'erreur personnalisé.
        passNew2.setCustomValidity('Les mots de passe sont différents');

        // Met à jour le div d'erreur.
        errorDiv.style.display = 'block';

        // Affiche le message d'erreur sur le formulaire.
        passNew2.reportValidity();
        return;
    } else {
        // Si les mots de passe sont identiques, cache le div d'erreur.
        errorDiv.style.display = 'none';
    }


    const fetchHandler = new FetchHandler(true);

    const data = {
        node : 'action',
        action : 'user_self_modif_pass',
        module_personnal : true,
        pass_old: passOld.value,
        pass_new: passNew1.value
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