<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script src="sweetalert2.all.min.js"></script>

<div class="rounded border mx-4 p-3">
    <h4 style="font-weight: bold; font-size: 1.8em">Szybka wiadomość</h4>

    <div id="messages-select-box"></div>
</div>

<script>
    const messagesSelectBox = document.getElementById('messages-select-box');

    axios.get('{{ route('fast-response.jsonIndex') }}').then((response) => {
        response.data.fastResponses.forEach((button) => {
            const buttonElement = document.createElement('button');

            buttonElement.classList.add('btn', 'btn-primary', 'm-2');

            buttonElement.innerText = button.title;

            buttonElement.setAttribute('onclick', 'save(' + button.id + ')');

            messagesSelectBox.appendChild(buttonElement);
        })
    });

    const save = async (id) => {
        const response = await axios.post(`/fast-response/${id}/{{ $order->id }}/send`);

        if (response.data.success) {
            swal.fire('Wysłano', 'Wysłano wiadomość', 'success');
        } else {
            alert('Wystąpił błąd');
        }
    }
</script>
