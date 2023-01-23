

class AllegroChat {

    constructor() {
        this.iconWrapper = $('.allegro-chat-icon-wrapper');
        this.iconCounter = $('.allegro-chat-icon-counter');
        this.allegroChatCheckUnreadedThreads();

        setInterval(() => {
            this.allegroChatCheckUnreadedThreads();
        }, 60000);

        this.initListeners();
    }

    initListeners() {
        this.iconWrapper.on('click', () => this.bookThread());
    }

    async allegroChatCheckUnreadedThreads() {
        const url = 'admin/allegro/checkUnreadedThreads';
        this.unreadedThreads = await ajaxPost({}, url, true);

        this.numberOfUnreadedMsgs = this.unreadedThreads?.length || 0;

        this.iconCounter.text(this.numberOfUnreadedMsgs);
    }

    async bookThread() {
        this.iconWrapper.addClass('loader-2');
        const url = 'admin/allegro/bookThread';
        const data = {
            unreadedThreads: this.unreadedThreads,
        };
        this.currentThreadId = await ajaxPost(data, url);
        this.iconWrapper.removeClass('loader-2');

        if(this.currentThreadId == 'empty') {
            toastr.error('Wiadomości zostały przypisane do innych użytkowników. Proszę spróbować później');
            return false;
        }
        this.openChatWindow();
    }

    async openChatWindow() {
        let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
        width=600,height=300,left=100,top=100`;

        const url = 'admin/allegro/getMessages/'+this.currentThreadId;
        const res = await ajaxPost({}, url);
        
        if(!res.messages) return false;

        const chatWindow = open('about:blank', 'allegroChat', params);

        let html = `<div style="font-size:30px">Welcome!</div>`;
        chatWindow.document.body.insertAdjacentHTML('afterbegin', html);
    }
}

$(function() {
    const allegroChat = new AllegroChat();
});