var Chart = function(i)
{
    // If we're not running jQuery, ABORT.
    if ($ == undefined) return;

    // Get the chart data
    this.chartData = charts[i];

    // Find certain key elements
    this.$chart = $('#'+this.chartData.id);
    this.$containerControls = this.$chart.find('.chart-controls-container');
    this.$containerScroll   = this.$chart.find('.chart-scroll-container');

    this.$info = this.$chart.find('.chart-info');

    // Dimensions for the event information box
    this.infoWidth = 200;
    this.infoPadding = 20;

    // Sort the events by their ID
    this.events = [];
    for (var i = 0; i < this.chartData.events.length; i++)
    {
        this.events[this.chartData.events[i].id_event] = this.chartData.events[i];
    }

    // Add binds (window resize, click, etc.)
    this.addBinds();

    // Update/initialize the chart's size
    this.updateSizes();

    // Show the information area (hidden before JavaScript comes in)
    this.$info.show();
};

Chart.prototype.addBinds = function()
{
    var self = this;

    // Make sure the chart scales correctly on window resize.
    $(window).resize(function() {
        self.updateSizes();
    });

    // Show information about an event when it's clicked on
    this.$chart.find('.chart-event').each(function() {
        $(this).on('click', function() {
            self.showInfo($(this).data('id-event'));
        });
    });
};

Chart.prototype.updateSizes = function()
{
    var chartWidth = this.$chart.width();
    var chartContainerWidth = chartWidth - this.infoWidth - this.infoPadding;

    this.$containerControls.width(chartContainerWidth);
    this.$containerScroll.width(chartContainerWidth);

    this.$info.width(this.infoWidth);
};

Chart.prototype.showInfo = function(id_event)
{
    if (this.events[id_event] === undefined) return;

    var eventData = this.events[id_event];

    var $infoContainer = $('<div></div>');
    $infoContainer.addClass('chart-info-box-container');

    $infoContainer.append('<h1>'+eventData.event_name+'</h1>');

    this.$info.html($infoContainer);

    console.log(this.events[id_event]);
};