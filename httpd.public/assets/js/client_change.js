function clientChange(select){
    let params = {client_id : select.value};

    // Fetch the HTML content from the server
    fetch('client_change.php', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        },
        body: JSON.stringify(params),
    })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.result === 'true') {
                getContent('default',0);
                getNavBar('default',0);
                getNavItem();
            } else {
                // do nothing
            }
        })
        .catch(error => {
            console.log('Fetch error: ', error);
        });
}
document.addEventListener("DOMContentLoaded", function() {
    getContent();
});