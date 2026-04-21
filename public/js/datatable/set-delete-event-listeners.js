const setDeleteEventListeners = () => {
    setTimeout(() => {
        const deleteLinks = document.querySelectorAll('#delete');
        console.log(deleteLinks);
        deleteLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.getAttribute('href');

                swal.fire({
                    title: 'Jesteś pewien usuwania?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    }, 1000);
}

setDeleteEventListeners();
