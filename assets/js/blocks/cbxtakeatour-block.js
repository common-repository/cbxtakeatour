'use strict';

(function (blocks, element, components, editor, ServerSideRender, blockEditor) {
    var el                = element.createElement,
        registerBlockType = blocks.registerBlockType,

        //InspectorControls = editor.InspectorControls,
        InspectorControls = blockEditor.InspectorControls,

        //ServerSideRender = components.ServerSideRender, //this is now direct part of wp.serverSideRender

        RangeControl      = components.RangeControl,
        Panel             = components.Panel,
        PanelBody         = components.PanelBody,
        PanelRow          = components.PanelRow,
        TextControl       = components.TextControl,
        //NumberControl = components.NumberControl,
        TextareaControl   = components.TextareaControl,
        CheckboxControl   = components.CheckboxControl,
        RadioControl      = components.RadioControl,
        SelectControl     = components.SelectControl,
        ToggleControl     = components.ToggleControl,
        //ColorPicker = components.ColorPalette,
        //ColorPicker = components.ColorPicker,
        //ColorPicker = components.ColorIndicator,
        PanelColorPicker  = editor.PanelColorSettings,
        DateTimePicker    = components.DateTimePicker,
        HorizontalRule    = components.HorizontalRule,
        ExternalLink      = components.ExternalLink;

    //var SVG =  components.SVG;

    //var MediaUpload = wp.editor.MediaUpload;


    /*var iconEl = el('svg', {
            width: 20,
            height: 20
        },
        el('path', {
            d: "M39.7,56.8v-1.2c0-0.2,0-0.3,0-0.5c-0.1-2.6-1.4-5.9-3.9-7.4c-1.2-0.7-3.1-1.3-5.6-0.1   c-3.6,1.7-5.6,6.9-5.9,11c-0.2,1.8-0.1,3.8,0.2,6.2c0.1,0.5,0.7,2.5,1,3.4c0.9,3,1.1,3.7,2,3.7c0.1,0,0.2,0,0.3,0l0.2,0   c0,0.4,0.1,0.9,0.2,1.3l0.2,7.5l-5,8.8c-0.2,0.3-0.2,0.7-0.1,1c0.1,0.3,0.3,0.6,0.7,0.8l3.7,1.7c0.2,0.1,0.4,0.1,0.5,0.1   c0.4,0,0.7-0.2,1-0.5l5.9-7.3l2.1,6.8c0.2,0.5,0.6,0.9,1.2,0.9h3.7c0.3,0,0.7-0.1,0.9-0.4c0.2-0.2,0.4-0.6,0.4-0.9L43,78.9   c0-0.1,0-0.2,0-0.3l-3.2-11.1v-0.9c0.5,0.3,1,0.6,1.5,0.9c0.7,0.4,1.4,0.8,2.1,1.2c0.2,0.1,0.4,0.2,0.7,0.2c0.4,0,0.8-0.2,1-0.6   l1.7-2.6c0.3-0.5,0.3-1.1-0.1-1.5L39.7,56.8z M42.6,65.4c-1-0.6-1.9-1.2-2.8-1.7v-3.2l4.4,4.8l-0.5,0.8   C43.3,65.8,42.9,65.6,42.6,65.4z M37.2,65v1.4h-4.9l-0.2-2.5l-0.3-3.3c0.4-0.6,0.8-1.4,1.2-2.2c0.6-1.2,1.3-2.5,1.9-3.9   c0.3-0.6,0-1.4-0.6-1.7c-0.6-0.3-1.4,0-1.7,0.6c-0.8,1.7-2.2,4.6-3.2,6.1c-0.2,0.2-0.2,0.5-0.2,0.8l0.5,6.3L30,69l-1.5,0.2   c-0.2-0.5-0.4-1.1-0.6-1.8c-0.4-1.3-0.8-2.7-0.9-3c-0.3-2.2-0.4-4.1-0.2-5.7c0.3-3.4,2-7.7,4.5-8.9c0.6-0.3,1.1-0.4,1.7-0.4   c0.5,0,1,0.1,1.5,0.4c1.5,0.9,2.6,3.1,2.7,5.4c0,0.1,0,0.3,0,0.4v1.7v0v3.2V62v1.5V65z M27.9,90.1l-1.6-0.8l4.5-7.9   c0.1-0.2,0.2-0.4,0.2-0.7l-0.1-3.7l3.1,4.1l0.3,1.1L27.9,90.1z M40.5,79.2l0.4,11.2h-1.5l-3.2-10.1c0-0.1-0.1-0.3-0.2-0.4l-5.3-7.1   c0,0,0-0.1,0-0.1c0-0.1,0-0.1-0.1-0.2c-0.1-0.2-0.1-0.6-0.1-1l1-0.1c0.7-0.1,1.1-0.7,1.1-1.3l-0.1-1.1h5L40.5,79.2z"
        }),
        el('path', {
            d: "M71.8,34.6c-2.5,0-4.5,1.7-5.1,4h-8.8c-0.6-2.3-2.6-4-5.1-4c-2.5,0-4.5,1.7-5.1,4H39c-0.6-2.3-2.6-4-5.1-4   c-2.9,0-5.3,2.4-5.3,5.2s2.4,5.2,5.3,5.2c2.5,0,4.5-1.7,5.1-4h8.8c0.6,2.3,2.6,4,5.1,4c2.5,0,4.5-1.7,5.1-4h8.8   c0.6,2.3,2.6,4,5.1,4c2.9,0,5.2-2.4,5.2-5.2S74.7,34.6,71.8,34.6z M71.8,42.6c-1.1,0-2-0.6-2.4-1.5c-0.2-0.4-0.3-0.8-0.3-1.2   s0.1-0.9,0.3-1.2c0.5-0.9,1.4-1.5,2.4-1.5c1.5,0,2.8,1.2,2.8,2.8S73.4,42.6,71.8,42.6z M55.6,39.8c0,0.5-0.1,0.9-0.3,1.2   c-0.5,0.9-1.4,1.5-2.4,1.5c-1.1,0-2-0.6-2.4-1.5c-0.2-0.4-0.3-0.8-0.3-1.2s0.1-0.9,0.3-1.2c0.5-0.9,1.4-1.5,2.4-1.5   c1.1,0,2,0.6,2.4,1.5C55.5,38.9,55.6,39.4,55.6,39.8z M36.6,39.8c0,0.5-0.1,0.9-0.3,1.2c-0.5,0.9-1.4,1.5-2.4,1.5   c-1.5,0-2.8-1.2-2.8-2.8s1.2-2.8,2.8-2.8c1.1,0,2,0.6,2.4,1.5C36.5,38.9,36.6,39.4,36.6,39.8z"
        })
    );
*/


    /*var iconEl = () => (
        <Icon icon="screenoptions" className="example-class" />
        );*/


    //console.log(ServerSideRender);

    registerBlockType('codeboxr/cbxtakeatour', {
        title: cbxtakeatour_block.block_title,
        // icon: iconEl,
        category: cbxtakeatour_block.block_category,

        /*
         * In most other blocks, you'd see an 'attributes' property being defined here.
         * We've defined attributes in the PHP, that information is automatically sent
         * to the block editor, so we don't need to redefine it here.
         */
        edit: function (props) {

            var $pro_controls = '';

            if (typeof cbxtakeatour_block.pro !== 'undefined') {
                $pro_controls = el(SelectControl, {
                    label   : cbxtakeatour_block.general_settings.displayonce,
                    options : cbxtakeatour_block.general_settings.yes_no_options,
                    onChange: (value) => {
                        props.setAttributes({
                            displayonce: value
                        });
                    },
                    value   : props.attributes.displayonce
                });
            }

            return [
                /*
                 * The ServerSideRender element uses the REST API to automatically call
                 * php_block_render() in your PHP code whenever it needs to get an updated
                 * view of the block.
                 */
                el(ServerSideRender, {
                    block     : 'codeboxr/cbxtakeatour',
                    attributes: props.attributes
                }),


                el(InspectorControls, {},
                    // 1st Panel – Form Settings
                    el(PanelBody, {
                            title      : cbxtakeatour_block.general_settings.title,
                            initialOpen: true
                        },
                        el(SelectControl, {
                            label   : cbxtakeatour_block.general_settings.id,
                            options : cbxtakeatour_block.general_settings.id_options,
                            onChange: (value) => {
                                props.setAttributes({
                                    id: parseInt(value)
                                });
                            },
                            type    : 'number',
                            value   : Number(props.attributes.id)
                        }),
                        el(TextControl, {
                            label   : cbxtakeatour_block.general_settings.button_text,
                            onChange: (value) => {
                                props
                                    .setAttributes({
                                        button_text: value
                                    });
                            },
                            value   : props.attributes.button_text
                        }),
                        el(SelectControl, {
                            label   : cbxtakeatour_block.general_settings.display,
                            options : cbxtakeatour_block.general_settings.yes_no_options,
                            onChange: (value) => {
                                props.setAttributes({
                                    display: value
                                });
                            },
                            value   : props.attributes.display
                        }),
                        el(SelectControl, {
                            label   : cbxtakeatour_block.general_settings.auto_start,
                            options : cbxtakeatour_block.general_settings.yes_no_options,
                            onChange: (value) => {
                                props.setAttributes({
                                    auto_start: value
                                });
                            },
                            value   : props.attributes.auto_start
                        }),
                        el(SelectControl, {
                            label   : cbxtakeatour_block.general_settings.block,
                            options : cbxtakeatour_block.general_settings.yes_no_options,
                            onChange: (value) => {
                                props.setAttributes({
                                    block: value
                                });
                            },
                            value   : props.attributes.block
                        }),
                        el(SelectControl, {
                            label   : cbxtakeatour_block.general_settings.align,
                            options : cbxtakeatour_block.general_settings.align_options,
                            onChange: (value) => {
                                props.setAttributes({
                                    align: value
                                });
                            },
                            value   : props.attributes.align
                        }),
                        $pro_controls
                    )
                )

            ];
        },
        // We're going to be rendering in PHP, so save() can just return null.
        save: function () {
            return null;
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.editor,
    window.wp.serverSideRender,
    window.wp.blockEditor
));