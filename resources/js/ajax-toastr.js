// resources/js/ajax-toastr.js
import toastr from './toastr';

const AjaxNotifications = {
    handle: function (response) {
        if (response.messages) {
            for (let type in response.messages) {
                if (response.messages.hasOwnProperty(type)) {
                    let messages = response.messages[type];
                    if (typeof messages === 'string') {
                        messages = [messages];
                    }
                    messages.forEach(message => {
                        toastr[type](message);
                    });
                }
            }
        }
    },

    success: function (message) {
        toastr.success(message);
    },

    error: function (message) {
        toastr.error(message);
    },

    warning: function (message) {
        toastr.warning(message);
    },

    info: function (message) {
        toastr.info(message);
    }
};

export default AjaxNotifications;
