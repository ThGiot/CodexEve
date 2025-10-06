import FetchHandler from '../classes/FetchHandler.js';
import { showToast } from '../classes/toastHelper.js';
const fetcher = new FetchHandler(true);
export function default_recover_moka(){
    var myModal = new bootstrap.Modal(document.getElementById('mokaRecover'));
    const data = {
        node: "action",
        action: "moka_recover",
        code : document.getElementById('code').value
      };
    
      fetcher.handleResponse(
        data,
        response => {
            document.getElementById('contentModal').innerHTML = response.message;
            myModal.show();
            let secondsRemaining = 10;
          
            const countdownElement = document.getElementById('decompte');

            // Mettre à jour le texte immédiatement
            countdownElement.innerHTML = 'Redirection dans ' + secondsRemaining + ' secondes...';

            const interval = setInterval(() => {
                secondsRemaining--;
                countdownElement.innerHTML = 'Redirection dans ' + secondsRemaining + ' secondes...';

                if (secondsRemaining <= 0) {
                    clearInterval(interval);
                    window.location.href = 'deconnexion.php';
                }
            }, 1000);
            
        },
        error => {
            document.getElementById('contentModal').innerHTML = error.message;
            myModal.show();
        }
    );


 

}