class AllegroChatInitializer {
    
    constructor(iconWrapper, iconCounter, ajaxPath, paths, mode) {
        this.iconWrapper = iconWrapper;
        this.iconCounter = iconCounter;
        this.ajaxPath = ajaxPath;
        this.paths = paths;
        this.mode = mode;

        this.allegroChatCheckUnreadedThreads();

        setInterval(() => {
            this.allegroChatCheckUnreadedThreads();
        }, 60000);

        this.initListeners();
    }
    
    initListeners() {
        this.iconWrapper.on('click', () => this.bookThread());
        $('.allegro-thread').on('click', e => this.messagesPreview(e));
    }

    async allegroChatCheckUnreadedThreads() {
        const url = this.ajaxPath + this.paths.checkUnreadedThreads;
        this.unreadedThreads = await ajaxPost({}, url);

        const numberOfUnreadedMsgs = this.unreadedThreads?.length || 0;

        numberOfUnreadedMsgs > 0 ? this.iconCounter.removeClass('hidden') : this.iconCounter.addClass('hidden');

        this.iconCounter.text(numberOfUnreadedMsgs);
    }

    async bookThread() {

        this.iconWrapper.addClass('loader-2');

        const url = this.ajaxPath + this.paths.bookThread;
        const data = {
            unreadedThreads: this.unreadedThreads,
        };
        const currentThread = await ajaxPost(data, url);

        if(this.mode == 'disputes') {
            if(currentThread.error) {
                toastr.error(currentThread.error);
                this.iconWrapper.removeClass('loader-2');
                return false;
            }
            this.openDisputedOrder(currentThread.order_id, currentThread.id);
            this.iconWrapper.removeClass('loader-2');
            return false;
        }

        if(!currentThread.allegro_thread_id) {
            toastr.error('Wiadomości zostały przypisane do innych konsultantów. Proszę spróbować później.');
            this.iconWrapper.removeClass('loader-2');
            return false;
        }
        const threadId = currentThread.allegro_thread_id;
        const nickname = currentThread.allegro_user_login;

        const isChatWindow = this.openChatWindow(threadId, nickname);

        if(isChatWindow) {
            this.openOrders(threadId, nickname);
        }
    }
    
    openDisputedOrder(orderId, disputeId) {
        window.open(
          `${this.ajaxPath}orders/${orderId}/edit`,
          '_blank'
        );
        window.open(
          `${this.ajaxPath}disputes/view/${disputeId}`,
          '_blank'
        );
    }
    
    openOrders(threadId, nickname) {
        // handle open new window with order
        let dtOrders = window.localStorage.getItem('DataTables_dataTable_/admin/orders');
        if(dtOrders) {
            dtOrders = JSON.parse(dtOrders);
            dtOrders.columns[26].search.search = nickname;
            window.localStorage.setItem('DataTables_dataTable_/admin/orders', JSON.stringify(dtOrders))
        }
        open(this.ajaxPath + 'orders', 'orders_'+threadId);
    }

    async messagesPreview(e) {

        if(!this.paths.messagesPreview) return false;

        $(e.currentTarget).addClass('loader-2');
        const threadId = $(e.currentTarget).find('.allegro-thread-id').text();
        const url = this.ajaxPath + this.paths.messagesPreview + threadId;
        const messages = await ajaxPost({}, url);

        if(!messages) {
            $(e.currentTarget).removeClass('loader-2');
            toastr.error('Coś poszło nie tak, prosimy spróbować raz jeszcze');
            return false;
        }

        // get incoming message for take user login
        const outgoingMsg = messages.find(msg => !msg.is_outgoing);

        if(!outgoingMsg) {
            $(e.currentTarget).removeClass('loader-2');
            toastr.error('Coś poszło nie tak, prosimy spróbować raz jeszcze');
            return false;
        }

        let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
        width=700,height=450,left=100,top=100`;

        open(this.ajaxPath+'allegro/chat', 'allegro_chat_preview', params);

        const chatWindowParams = {
            messages,
            threadId,
            nickname: outgoingMsg.allegro_user_login,
            isPreview: true,
        };

        window.localStorage.setItem('preview_allegro_chat_storage', JSON.stringify(chatWindowParams));
        $(e.currentTarget).removeClass('loader-2');

    }
    async openChatWindow(threadId, nickname) {

        if(!this.paths.getMessages) return false;

        const url = this.ajaxPath + this.paths.getMessages + threadId;
        let messages = await ajaxPost({}, url);
        
        this.iconWrapper.removeClass('loader-2');

        if(!messages) {
            toastr.error('Coś poszło nie tak, prosimy spróbować raz jeszcze');
            return false;
        }

        let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
        width=700,height=700,left=100,top=100`;

        open(this.ajaxPath+'allegro/chat', 'allegro_chat', params);

        const chatWindowParams = {
            messages,
            threadId,
            nickname,
            isPreview: false,
        };

        window.localStorage.setItem('preview_allegro_chat_storage', JSON.stringify(chatWindowParams));
    }
}
