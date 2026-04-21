const showHide  = () => {
    setTimeout(() => {
        let elements = document.getElementsByClassName('hidden');

        elements = Array.from(elements);

        for (let i = 0; i < elements.length; i++) {
            elements[i].classList.remove('hidden');
            console.log(elements[i] )
        }
    }, 1000)
}

showHide();
