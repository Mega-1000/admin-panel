const emit = (name: string) => {
    window.dispatchEvent(new Event(name))
}

const listen = (name: string, callback: Function) => {
    window.addEventListener(name, () => callback)
}

export default {
    emit,
    listen
}
