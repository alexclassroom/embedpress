/**
 * WordPress dependencies
 */

import react from 'react';
import ControlHeader from '../../common/control-heading';
import { getParams } from '../functions';
import LockControl from '../../common/lock-control';
import ContentShare from '../../common/social-share-control';

const { isShallowEqualObjects } = wp.isShallowEqual;
const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;
const { addFilter } = wp.hooks;

const {
    SelectControl,
    RangeControl,
    ToggleControl,
    TextControl,
    PanelBody,
    ColorPalette,
    FontSizePicker,
    // AlignmentMatrixControl
} = wp.components;

// import { __experimentalAlignmentMatrixControl as AlignmentMatrixControl } from '@wordpress/components';

const {
    InspectorControls
} = wp.blockEditor;

export const init = () => {
    addFilter('embedpress_block_rest_param', 'embedpress', getCalendlyParams, 10);
}

export const getCalendlyParams = (params, attributes) => {
    if (!url) {
        return params;
    }

    // which attributes should be passed with rest api.
    const defaults = {
        cEmbedType: 'inline',
        hideCookieBanner: 0,
        hideEventTypeDetails: 0,
        cBackgroundColor: '643cd5',
        cTextColor: '2cff2c',
        cButtonLinkColor: '0069FF',
        cPopupButtonText: 'Schedule time with me',
        cPopupButtonBGColor: '0069FF',
        cPopupButtonTextColor: 'FFFFFF',
        cPopupLinkText: 'Schedule time with me',


    };

    return getParams(params, attributes, defaults);
}

export const isCalendly = (url) => {
    return url.match(/^https:\/\/calendly\.com\/.*/);
}

/**
 *
 * @param {object} attributes
 * @returns
 */
export const useCalendly = (attributes) => {
    // which attribute should call embed();
    const defaults = {
        cEmbedType: null,
        hideCookieBanner: null,
        hideEventTypeDetails: null,
        cBackgroundColor: null,
        cTextColor: null,
        cButtonLinkColor: null,
        cPopupButtonText: null,
        cPopupButtonBGColor: null,
        cPopupButtonTextColor: null,
        cPopupLinkText: null
    };
    const param = getParams({}, attributes, defaults);
    const [atts, setAtts] = useState(param);

    useEffect(() => {
        const param = getParams(atts, attributes, defaults);
        if (!isShallowEqualObjects(atts || {}, param)) {
            setAtts(param);
        }
    }, [attributes]);

    return atts;
}

export default function Calendly({ attributes, setAttributes, isCalendly }) {
    const {
        cEmbedType,
        hideCookieBanner,
        hideEventTypeDetails,
        cBackgroundColor,
        cTextColor,
        cButtonLinkColor,
        cPopupButtonText,
        cPopupButtonBGColor,
        cPopupButtonTextColor,
        cPopupLinkText
    } = attributes;

    const isProPluginActive = embedpressObj.is_pro_plugin_active;

    const addProAlert = (e, isProPluginActive) => {
        if (!isProPluginActive) {
            document.querySelector('.pro__alert__wrap').style.display = 'block';
        }
    }

    const removeAlert = () => {
        if (document.querySelector('.pro__alert__wrap')) {
            document.querySelector('.pro__alert__wrap .pro__alert__card .button').addEventListener('click', (e) => {
                document.querySelector('.pro__alert__wrap').style.display = 'none';
            });
        }
    }


    const isPro = (display) => {
        const alertPro = `
		<div class="pro__alert__wrap" style="display: none;">
			<div class="pro__alert__card">
				<img src="../wp-content/plugins/embedpress/EmbedPress/Ends/Back/Settings/assets/img/alert.svg" alt=""/>
					<h2>Opps...</h2>
					<p>You need to upgrade to the <a href="https://wpdeveloper.com/in/upgrade-embedpress" target="_blank">Premium</a> Version to use this feature</p>
					<a href="#" class="button radius-10">Close</a>
			</div>
		</div>
		`;

        const dom = document.createElement('div');
        dom.innerHTML = alertPro;

        return dom;

    }

    if (!document.querySelector('.pro__alert__wrap')) {
        document.querySelector('body').append(isPro('none'));
        removeAlert();
    }

    const fontSizes = [
        {
            name: __('Small'),
            slug: 'small',
            size: 16,
        },
        {
            name: __('Medium'),
            slug: 'medium',
            size: 18,
        },
        {
            name: __('Large'),
            slug: 'big',
            size: 26,
        },
    ];

    const colors = [
        { name: '', color: 'red' },
        { name: '', color: 'green' },
        { name: '', color: 'blue' },
        { name: '', color: 'yellow' },
        { name: '', color: 'orange' },
    ];

    const fallbackFontSize = 16;

    return (
        (isCalendly) && (
            <PanelBody title={__("Calendly Controls")} initialOpen={false} className={'ep-opensea-options'}>
                <div>
                    <SelectControl
                        label={__("Embed Type", "embedpress")}
                        value={cEmbedType}
                        options={[
                            { label: 'Inline', value: 'inline' },
                            { label: 'Popup Button', value: 'popup_button' },
                            { label: 'Popup Text', value: 'popup_text' },
                        ]}
                        onChange={(cEmbedType) => setAttributes({ cEmbedType })}
                    />

                    {
                        (cEmbedType === 'popup_button') && (
                            <div>
                                <TextControl
                                    label="Popup Button Text"
                                    value={cPopupButtonText}
                                    onChange={(cPopupButtonText) => setAttributes({ cPopupButtonText })}
                                />
                                <TextControl
                                    label="Popup Button Background Color"
                                    value={cPopupButtonBGColor}
                                    onChange={(cPopupButtonBGColor) => setAttributes({ cPopupButtonBGColor })}
                                />
                            </div>
                        )
                    }

                    {
                        (cEmbedType === 'popup_text') && (
                            <div>
                                <ControlHeader headerText={'Popup Button Text Color'} />
                                <ColorPalette
                                    label={__("Popup Button Text Color")}
                                    colors={colors}
                                    value={cPopupButtonTextColor}
                                    onChange={(cPopupButtonTextColor) => setAttributes({ cPopupButtonTextColor })}
                                />
                                <TextControl
                                    label="Popup Link Text"
                                    value={cPopupLinkText}
                                    onChange={(cPopupLinkText) => setAttributes({ cPopupLinkText })}
                                />
                            </div>
                        )
                    }

                    <ToggleControl
                        label="Hide Cookie Banner"
                        checked={hideCookieBanner}
                        onChange={(hideCookieBanner) => setAttributes({ hideCookieBanner })}
                    />
                    <ToggleControl
                        label="Hide Event Type Details"
                        checked={hideEventTypeDetails}
                        onChange={(hideEventTypeDetails) => setAttributes({ hideEventTypeDetails })}
                    />
                    <ControlHeader headerText={'Background Color'} />
                    <ColorPalette
                        label={__("Background Color")}
                        colors={colors}
                        value={cBackgroundColor}
                        onChange={(cBackgroundColor) => setAttributes({ cBackgroundColor })}
                    />

                    <ControlHeader headerText={'Text Color'} />
                    <ColorPalette
                        label={__("Text Color")}
                        colors={colors}
                        value={cTextColor}
                        onChange={(cTextColor) => setAttributes({ cTextColor })}
                    />

                    <ControlHeader headerText={'Button Link Color'} />
                    <ColorPalette
                        label={__("Text Color")}
                        colors={colors}
                        value={cButtonLinkColor}
                        onChange={(cButtonLinkColor) => setAttributes({ cButtonLinkColor })}
                    />

                </div>
            </PanelBody>

        )
    )
}