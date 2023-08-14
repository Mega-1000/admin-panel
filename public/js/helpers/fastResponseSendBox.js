const messagesSelectBox = document.getElementById('messages-select-box');
document.addEventListener('DOMContentLoaded', () => {
    // Your code here
    axios.get('/fast-response/jsonIndex').then((response) => {
        response.data.fastResponses.forEach((button) => {
            const buttonElement = document.createElement('div');

            buttonElement.classList.add('btn', 'btn-primary', 'm-2');

            buttonElement.innerText = button.title;

            buttonElement.setAttribute('onclick', 'save(' + button.id + ')');

            messagesSelectBox.appendChild(buttonElement);
        })
    });
});

const save = async (id) => {
    const response = await axios.post(`/fast-response/${id}/{{ $order->id }}/send`);

    if (response.data.success) {
        swal.fire('Wysłano', 'Wysłano wiadomość', 'success');
    } else {
        alert('Wystąpił błąd');
    }
}
