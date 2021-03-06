/**
 * jQuery™ UI Toggles.
 *
 * Copyright: © 2012 (coded in the USA)
 * {@link http://www.websharks-inc.com WebSharks™}
 *
 * @author JasWSInc
 * @package WebSharks\Core
 * @since 120318
 */

(function($) // Works like a jQuery UI accordion.
	{
		$.fn.toggles = function(options, actions)
			{
				// Defaults.
				var defaults =
				{
					options: {
						activeByDefaultClass: 'active-by-default',
						header              : '> div > div > h3',
						isActive            : function(){},
						onToggle            : function(){}
					},
					actions: {
						showAll  : false,
						hideAll  : false,
						toggleAll: false
					}
				};

				// Define options object.
				if(typeof options === 'object')
					options = $.extend({}, defaults.options, options);
				else options = defaults.options;

				// Define actions object.
				if(typeof actions === 'object')
					actions = $.extend({}, defaults.actions, actions);
				else actions = defaults.actions;

				// Show all.
				var showAll = function()
					{
						var $toggles = $(this);

						if(!$toggles.data('toggles-setup'))
							return;

						var options = $toggles.data('toggle-options');

						$toggles.find(options.header)
							.removeClass('ui-state-default ui-corner-all').addClass('ui-state-active ui-corner-top')
							.find('> span.ui-icon').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s').end()
							.next().show().addClass('ui-accordion-content-active');

						options.onToggle.apply(this, [getToggleStates.apply(this)]);
					};

				// Hide all.
				var hideAll = function()
					{
						var $toggles = $(this);

						if(!$toggles.data('toggles-setup'))
							return;

						var options = $toggles.data('toggle-options');

						$toggles.find(options.header)
							.removeClass('ui-state-active ui-corner-top').addClass('ui-state-default ui-corner-all')
							.find('> span.ui-icon').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e').end()
							.next().hide().removeClass('ui-accordion-content-active');

						options.onToggle.apply(this, [getToggleStates.apply(this)]);
					};

				// Toggle all.
				var toggleAll = function()
					{
						var $toggles = $(this);

						if(!$toggles.data('toggles-setup'))
							return;

						var options = $toggles.data('toggle-options');

						if(getToggleStates.apply(this).inactive.length)
							showAll.apply(this);
						else hideAll.apply(this);
					};

				// Set active toggle panels.
				var setActivePanels = function()
					{
						var $toggles = $(this);
						var specificTogglesActive = false;

						if(!$toggles.data('toggles-setup'))
							return;

						var options = $toggles.data('toggle-options');

						$toggles.find(options.header + ' > a')
							.each(function()
							      {
								      if(options.isActive.apply(this))
									      {
										      // Seems like a bug in PhpStorm.
										      // The `.parent()` arg is optional!
										      // noinspection JSCheckFunctionSignatures
										      headerToggle.apply($(this).parent().get(0));
										      specificTogglesActive = true;
									      }
							      });

						if(!specificTogglesActive)
							$toggles.find(options.header + '.' + options.activeByDefaultClass)
								.each(function(){ headerToggle.apply(this); });
					};

				// Get toggle states.
				var getToggleStates = function()
					{
						var $toggles = $(this);
						var active = [], inactive = [];

						if(!$toggles.data('toggles-setup'))
							return {active: active, inactive: inactive};

						var options = $toggles.data('toggle-options');

						$toggles.find(options.header)
							.each(function()
							      {
								      if($(this).hasClass('ui-state-active'))
									      active.push(this);
								      else inactive.push(this);
							      });

						return {active: active, inactive: inactive};
					};

				// Toggle specific panel.
				var headerToggle = function()
					{
						var $header = $(this);

						$header.toggleClass('ui-state-default ui-corner-all ui-state-active ui-corner-top')
							.find('> span.ui-icon').toggleClass('ui-icon-triangle-1-e ui-icon-triangle-1-s').end()
							.next().toggle().toggleClass('ui-accordion-content-active');
					};

				// Setup/initialize toggles.
				var setupInitialize = function()
					{
						var toggles = this;
						var $toggles = $(toggles);

						if(!$toggles.data('toggles-setup'))
							{
								$toggles.data('toggle-options', options).data('toggles-setup', true)

									.addClass('ui-accordion ui-widget ui-helper-reset ui-accordion-icons')

									.find(options.header)
									.addClass('ui-accordion-header ui-helper-reset ui-state-default ui-corner-all')
									.prepend('<span class="ui-icon ui-icon-triangle-1-e"></span>')

									.hover(function(){ $(this).toggleClass('ui-state-hover'); })

									.click(function() // Toggle.
									       {
										       headerToggle.apply(this);
										       options.onToggle.apply(toggles, [getToggleStates.apply(toggles)]);
										       return false;
									       })

									.next().addClass('ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom').hide();

								$toggles.css({visibility: 'visible'}), setActivePanels.apply(this);
							}
					};

				// Process actions.
				if(actions.showAll)
					return this.each(showAll);

				else if(actions.hideAll)
					return this.each(hideAll);

				else if(actions.toggleAll)
					return this.each(toggleAll);

				// Else setup/initialize.
				return this.each(setupInitialize);
			};
	})(jQuery);