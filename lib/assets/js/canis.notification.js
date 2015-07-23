var notificationNavItem;

CanisInstructionHandler.prototype.handleNotificationAction = function() {
    var self = this;
    if (notificationNavItem) {
        notificationNavItem.refresh(true);
    }
    return true;
};

function NotificationNavItem($element, config) {
    this.isInitializing = true;
    CanisComponent.call(this, $element);
    var self = this;
    this.pendingRefresh = false;
    this.firstOpen = true;
    this.updateInterval = null;
    this.updateIntervalResource = null;
    this.opened = false;
    this.mostRecentEvent = false;
    this.$element = $element;
    $element.addClass('notification-trigger');
    this.$element.hide().removeClass('hidden');
    this.$link = $('a', $element).first();
    this.$span = $('span', this.$link).first();
    this.$canvas = $('<div />', {'class': 'notification-canvas'});
    this.$itemList = $('<div />', {'class': 'list-group'}).appendTo(this.$canvas);
    this.$link.popover({
        'placement': 'bottom',
        'content': this.$canvas,
        'title': 'Background Tasks',
        'html': true,
        'trigger': 'manual'
    });
    this.$link.click(function() {
        if (self.opened) {
            self.close();
        } else {
            self.open();
        }
        return false;
    });
    $(document).on('click', function(e) {
        if ($(e.target).is(self.$link) || $(e.target).is(self.$canvas) || self.$canvas.find(e.target).length !== 0) {
            return;
        }
        self.close();
    });
    $(document).keyup(function(e) {
        if (e.keyCode == 27) {   // esc
            self.close();
        }
    });
    this.items = {};
    this.config = config['_'];
    this.handleData(config);
    this.isInitializing = false;
}
NotificationNavItem.prototype = jQuery.extend(true, {}, CanisComponent.prototype);

NotificationNavItem.prototype.setInterval = function(time) {
    var self = this;
    if (time === undefined) {
        time = 5000;
    }
    if (time !== this.updateInterval) {
        clearInterval(this.updateIntervalResource);
        this.updateIntervalResource = setInterval(function() {
            if (!self.pendingRefresh) {
                self.pendingRefresh = true;
                self.refresh();
            }
        }, time);
        this.updateInterval = time;
    }
};

NotificationNavItem.prototype.handleData = function(data) {
    var self = this;
    self.pendingRefresh = false;
    var foundItems = false;
    var remainingIds = _.keys(self.items);
    if (_.isEmpty(data.items)) {
        this.setInterval(60000);
    } else {
        this.setInterval(5000);
    }
    jQuery.each(data.items, function(id, item) {
        remainingIds = _.without(remainingIds, id);
        foundItems = true;
        if (self.items[id] === undefined) {
            self.items[id] = {};
            self.items[id].$canvas = $('<div />', {'class': 'list-group-item'}).prependTo(self.$itemList);
            self.items[id].$name = $('<h5 />', {'class': 'list-group-item-heading'}).appendTo(self.items[id].$canvas);
            self.items[id].$duration = $('<span />', {'class': 'label label-primary'}).appendTo(self.items[id].$canvas);
            self.items[id].$status = $('<span />', {'class': 'label'}).appendTo(self.items[id].$canvas);
            self.items[id].$actions = $('<div />', {'class': 'btn-group btn-group-xs pull-right'}).hide().appendTo(self.items[id].$canvas);
            self.items[id].$dismiss = $('<a />', {'class': 'notification-dismiss close pull-right', 'href': item.data.dismissUrl, 'data-handler': 'background'}).hide().html('<span class=\'aria-hidden\'>&times;</span>').prependTo(self.items[id].$canvas);

        }
        if (item.data.dismissUrl) {
            self.items[id].$dismiss.attr('href', item.data.dismissUrl).show();
        } else {
            self.items[id].$dismiss.hide();
        }
        self.items[id].$canvas.show();
        self.items[id].$name.html(item.data.descriptor);
        self.items[id].$status.removeClass('label-default label-primary label-success label-info label-warning label-danger');
        switch (item.status) {
            case 'queued':
                self.items[id].$status.html('Queued');
                self.items[id].$status.addClass('label-default');
            break;
            case 'starting':
            case 'running':
                self.items[id].$status.html('Running');
                self.items[id].$status.addClass('label-primary');
            break;
            case 'error':
                self.items[id].$status.html('Error');
                self.items[id].$status.addClass('label-danger');
            break;
            case 'success':
                self.items[id].$status.html('Done');
                self.items[id].$status.addClass('label-success');
            break;
            default:
                self.items[id].$status.html('Unknown');
                self.items[id].$status.addClass('label-warning');
            break;
        }
        self.items[id].$status.attr('title', item.data.result.message);

        if (!_.isEmpty(item.data.actions)) {
            self.items[id].$actions.show();
            self.items[id].$actions.html('');
            jQuery.each(item.data.actions, function(index, action) {
                var $action = $('<a />', {'href': action.url, 'class': 'btn'}).html(action.label).appendTo(self.items[id].$actions);
                if (action.state === undefined) {
                    action.state = 'default';
                }
                $action.addClass('btn-' + action.state);

                delete action.url;
                delete action.label;
                delete action.state;
                $action.attr(action);

            });
        } else {
            self.items[id].$actions.hide();
        }
    });

    jQuery.each(remainingIds, function(index, id) {
        self.items[id].$canvas.remove();
        delete self.items[id];
    });

    if (foundItems) {
        this.$element.show();
        if (!self.firstOpen && data.mostRecentEvent && self.mostRecentEvent !== data.mostRecentEvent) {
            self.open();
        }
        self.mostRecentEvent = data.mostRecentEvent;
    } else {
        this.$element.hide();
    }
    self.firstOpen = false;
};

NotificationNavItem.prototype.open = function() {
    this.$link.popover('show');
    this.opened = true;
};

NotificationNavItem.prototype.close = function() {
    this.$link.popover('hide');
    this.opened = false;
};

NotificationNavItem.prototype.refresh = function(openAfter) {
    var self = this;
    var ajax = {};
    this.updateTimer = null;
    if (openAfter === undefined) {
        openAfter = false;
    }
    ajax['url'] = this.config.refreshUrl;
    ajax['success'] = function(result) {
        if (result['_'] === undefined) { return; }
        self.handleData(result);
        if (openAfter) {
            self.open();
        }
    }
    jQuery.ajax(ajax);
};

$preparer.add(function(context) {
    $('[data-notification]', context).each(function() {
        notificationNavItem = new NotificationNavItem($(this), $(this).data('notification'));
        $(this).attr('data-notification', false);
        return false;
    });
});
