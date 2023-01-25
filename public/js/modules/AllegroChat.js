

class AllegroChat {
    
    chatWindow = null;
    isChatWindowOpen = false;

    constructor() {
        this.iconWrapper = $('.allegro-chat-icon-wrapper');
        this.iconCounter = $('.allegro-chat-icon-counter');
        this.ajaxPath = window.location.pathname == '/admin' ? '/admin/' : '/admin/';
        this.currentThreadId = window.localStorage.getItem('allegroChatThreadId');
        this.nickname = window.localStorage.getItem('allegroChatNickname');
        this.allegroChatCheckUnreadedThreads();

        setInterval(() => {
            if(this.currentThreadId) return false;
            this.allegroChatCheckUnreadedThreads();
        }, 60000);

        this.initListeners();
    }

    initListeners() {
        this.iconWrapper.on('click', () => this.bookThread());
        $('.allegro-thread').on('click', e => this.messagesPreview(e));
    }

    async messagesPreview(e) {
        $(e.currentTarget).addClass('loader-2');
        const allegroThreadId = $(e.currentTarget).find('.allegro-thread-id').text();
        const url = this.ajaxPath + 'allegro/messagesPreview/'+allegroThreadId;
        const messages = await ajaxPost({}, url);

        $(e.currentTarget).removeClass('loader-2');

        if(!messages) {
            toastr.error('Coś poszło nie tak, prosimy spróbować raz jeszcze');
            return false;
        }

        let allegroClient = '';
        const messagesTemplate = messages.map(msg => {

            if(!msg.is_outgoing) allegroClient = msg.allegro_user_login;

            return this.makeSingleMessageTemplate(msg);
        }).join('');

        const chatTemplate = `
        <div class="allegro-chat-wrapper allegro-chat-wrapper-preview">
            <h2>Czat Allegro</h2>
            <h3>Dotyczy użytkownika Allegro: ${allegroClient}</h3>
            <h3>ID czatu na Allegro: ${allegroThreadId}</h3>
            <div class="allegro-msgs-wrapper">${messagesTemplate}</div>
        </div>
        `;

        const previewChatWindow = open(this.ajaxPath+'allegro-chat', 'allegro_chat_'+allegroThreadId);

        $(previewChatWindow).on('load', () => {
            previewChatWindow.document.body.innerHTML = chatTemplate;
            this.chatScrollDown();

            $(previewChatWindow.document.body).on('click', '.allegro-attachments-item', e => {
                this.downloadAttachment(e);
            });
        })
    }

    async allegroChatCheckUnreadedThreads() {
        const url = this.ajaxPath + 'allegro/checkUnreadedThreads';
        this.unreadedThreads = await ajaxPost({}, url, true);

        this.numberOfUnreadedMsgs = this.unreadedThreads?.length || 0;

        this.numberOfUnreadedMsgs > 0 ? this.iconCounter.removeClass('hidden') : this.iconCounter.addClass('hidden');

        this.iconCounter.text(this.numberOfUnreadedMsgs);
    }

    async bookThread() {

        this.iconWrapper.addClass('loader-2');

        if(!this.currentThreadId) {
            const url = this.ajaxPath + 'allegro/bookThread';
            const data = {
                unreadedThreads: this.unreadedThreads,
            };
            const currentThread = await ajaxPost(data, url);
            if(!currentThread.id) {
                toastr.error('Wiadomości zostały przypisane do innych użytkowników. Proszę spróbować później');
                this.iconWrapper.removeClass('loader-2');
                return false;
            }
            this.currentThreadId = currentThread.id;
            this.nickname = currentThread.interlocutor.login;
            window.localStorage.setItem('allegroChatThreadId', this.currentThreadId);
            window.localStorage.setItem('allegroChatNickname', this.nickname);
        }

        // handle open new window with order
        let dtOrders = window.localStorage.getItem('DataTables_dataTable_/admin/orders');
        if(dtOrders) {
            dtOrders = JSON.parse(dtOrders);
            dtOrders.columns[26].search.search = this.nickname;
            window.localStorage.setItem('DataTables_dataTable_/admin/orders', JSON.stringify(dtOrders))
        }
        open(this.ajaxPath + 'orders', 'orders_'+this.currentThreadId);

        this.openChatWindow();
    }

    async downloadAttachment(e) {
      const attachmentId = $(e.target).data('id');
      const attachmentName = $(e.target).text();
      const msgFooter = $(e.target).parent().parent();
      msgFooter.addClass('loader-2');

      const url = this.ajaxPath + 'allegro/downloadAttachment/'+attachmentId;
      const fileData = await ajaxPost({}, url);

      msgFooter.removeClass('loader-2');

      if(!fileData.content || !fileData.contentType) return false;
      saveFileAs(attachmentName, fileData.content, fileData.contentType);
    }
    
    makeSingleMessageTemplate(msg) {

        const type = msg.is_outgoing ? 'outgoing' : 'incoming';
        const attachments = JSON.parse(msg.attachments);

        const attachmentsTemplate = attachments && attachments.map(attachment => {
            if(attachment.status == 'UNSAFE' || attachment.status == 'EXPIRED') return ``;
            const attachmentId = attachment.url.split('/').pop();
            return `
                <div class="allegro-attachments-list">
                    <a class="allegro-attachments-item" href="javascript:;" data-id="${attachmentId}">${attachment.fileName}</a>
                </div>
            `;
        }).join('') || '';

        return `
            <div class="allegro-msg-wrapper ${type}">
                <div class="allegro-msg-header">
                    <div class="allegro-msg-date">[${msg.original_allegro_date}]</div>
                    <div class="allegro-msg-consultant">
                        Konsultant: <strong>${msg.user.name}</strong>
                    </div>
                    ${msg.subject ? `<div class="allegro-msg-subject">
                        <strong>Temat: ${msg.subject}</strong>
                    </div>` : ``}
                </div>
                <div class="allegro-msg-content">
                    ${msg.content}
                </div>
                <div class="allegro-msg-footer">
                    ${ msg.allegro_offer_id ? `<div>
                    <a href="https://allegro.pl/oferta/${msg.allegro_offer_id}" target="_blank" class="btn allegro-msg-offer-id">Przejdź do oferty</a></div>`
                     : `` }
                    ${ msg.allegro_order_id ? `<div>
                    <a href="https://allegro.pl/moje-allegro/sprzedaz/zamowienia/${msg.allegro_order_id}" target="_blank" class="btn allegro-msg-order-id">Przejdź do zamówienia</a></div>`
                     : `` }
                    ${attachmentsTemplate}
                    <div class="allegro-msg-id">Id wiadomości na Allegro: ${msg.allegro_msg_id}</div>
                </div>
            </div>
        `;
    }
    
    async closeChat() {
        const url = this.ajaxPath + 'allegro/exitChat/'+this.currentThreadId;
        await ajaxPost({}, url);
        window.localStorage.removeItem('allegroChatThreadId');
        window.localStorage.removeItem('allegroChatNickname');

        this.currentThreadId = false;
        this.nickname = false;
        this.chatWindow.close();
    }

    initChatWindowListeners() {

        // add Chat Window Listeners
        $(this.chatWindow.document.body).on('click', '.allegro-attachments-item', e => {
            this.downloadAttachment(e);
        });
        $(this.chatWindow.document.body).on('click', '.allegro-close-conversation', () => {
            this.closeChat();
        });
    }

    chatScrollDown() {
        setTimeout(() => {
            const msgsWrapper = $(this.chatWindow.document).find('.allegro-msgs-wrapper');
            msgsWrapper.trigger('focus');
            msgsWrapper.scrollTop(msgsWrapper[0].scrollHeight);
        }, 150);
    }

    async openChatWindow() {
        const url = this.ajaxPath + 'allegro/getMessages/'+this.currentThreadId;
        let messages = await ajaxPost({}, url);

        if(!messages) {
            toastr.error('Coś poszło nie tak, prosimy spróbować raz jeszcze');
            return false;
        }

        let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
        width=700,height=700,left=100,top=100`;

        let allegroClient = '';

        const messagesTemplate = messages.map(msg => {

            if(!msg.is_outgoing) allegroClient = msg.allegro_user_login;

            return this.makeSingleMessageTemplate(msg);
        }).join('');

        const chatTemplate = `
        <div class="allegro-chat-wrapper">
            <h2>Czat Allegro</h2>
            <h3>Dotyczy użytkownika Allegro: ${allegroClient}</h3>
            <h3>ID czatu na Allegro: ${this.currentThreadId}</h3>
            <div class="allegro-msgs-wrapper">${messagesTemplate}</div>
            <hr>
            <textarea class="allegro-textarea"></textarea>
            <div>
                <input type="file" value="Dodaj załącznik" />
            </div>
            <div class="btn allegro-send">Wyślij wiadomość</div>
            <div class="btn allegro-close-conversation">Zamknij konwersację</div>
        </div>
        `;

        this.iconWrapper.removeClass('loader-2');

        window.cw = this.chatWindow = open(this.ajaxPath+'allegro-chat', 'allegro_chat_'+this.currentThreadId, params);

        $(this.chatWindow).on('load', () => {
            this.isChatWindowOpen = true;
            this.chatWindow.document.body.innerHTML = chatTemplate;
            
            this.chatScrollDown();
            this.initChatWindowListeners();
        })
    }
}

$(function() {
    const allegroChat = new AllegroChat();
});