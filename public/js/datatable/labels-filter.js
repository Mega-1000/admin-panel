document.addEventListener('livewire:load', () => {
    const localStorageData = localStorage.getItem('labelId');

    Livewire.emit('localStorageDataUpdated', localStorageData ?? '');
});


Livewire.on('labelSelected', (labelId) => {
    const labelCurrent = window.localStorage.getItem('labelId');
    // if label is already selected then deselect it firstly explode string to array
    if (labelCurrent) {
        const labelCurrentArray = labelCurrent.split(',');

        if (labelCurrentArray.includes(labelId)) {
            const labelCurrentArrayFiltered = labelCurrentArray.filter(item => item !== labelId);
            window.localStorage.setItem('labelId', labelCurrentArrayFiltered.join(','));
            return;
        }
    }

    if (labelCurrent) {
        window.localStorage.setItem('labelId', labelCurrent + ',' + labelId);
    } else {
        window.localStorage.setItem('labelId', labelId);
    }

    Livewire.emit('localStorageDataUpdated', localStorage.getItem('labelId') ?? '');
});

Livewire.on('labelDeselected', () => {
    window.localStorage.removeItem('labelId');
    Livewire.emit('localStorageDataUpdated', '');
});
