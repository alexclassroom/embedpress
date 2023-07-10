import { useRef } from 'react';
import { isPro, removeAlert, addTipsTrick, removeTipsAlert, tipsTricksAlert, isInstagramFeed } from '../common/helper';
import LockControl from '../common/lock-control';
import ContentShare from '../common/social-share-control';
import Youtube from './InspectorControl/youtube';
import OpenSea from './InspectorControl/opensea';
import Wistia from './InspectorControl/wistia';
import Vimeo from './InspectorControl/vimeo';
import SlefHosted from './InspectorControl/selfhosted';
import { EPIcon, InfoIcon } from '../common/icons';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const {
    TextControl,
    NumberControl,
    PanelBody,
    SelectControl,
    ToggleControl
} = wp.components;

const {
    InspectorControls
} = wp.blockEditor;


export default function Inspector({ attributes, setAttributes, isYTChannel, isYTVideo, isYTLive, isOpensea, isOpenseaSingle, isWistiaVideo, isVimeoVideo, isSelfHostedVideo, isSelfHostedAudio }) {

    const {
        url,
        width,
        height,
        videosize,
        instaLayout,
        slidesShow,
        slidesScroll,
        carouselAutoplay,
        autoplaySpeed,
        transitionSpeed,
        carouselLoop,
        carouselArrows,
        carouselSpacing,
        lockContent,
        contentPassword,
        editingURL,
        embedHTML,
    } = attributes;

    const isProPluginActive = embedpressObj.is_pro_plugin_active;

    const inputRef = useRef(null);

    const roundToNearestFive = (value) => {
        return Math.round(value / 5) * 5;
    }

    if (!document.querySelector('.pro__alert__wrap')) {
        document.querySelector('body').append(isPro('none'));
        removeAlert();
    }

    if (!document.querySelector('.tips__alert__wrap')) {
        document.querySelector('body').append(tipsTricksAlert('none'));
        removeTipsAlert();
    }

    if ((isYTVideo || isYTLive || isVimeoVideo) && width === '600' && height === '450') {
        setAttributes({ height: '340' });
    }

    if (isSelfHostedAudio) {
        setAttributes({ height: '48' });
    }

    return (
        !editingURL && embedHTML && (
            <InspectorControls>
                {
                    !isOpensea && !isOpenseaSingle && (
                        <div>
                            <PanelBody title={__("General")}>

                                <div>

                                    {
                                        isYTLive && (
                                            <p className='ep-live-video-info'>{InfoIcon} {'The most recent live video will be seen.'}</p>
                                        )
                                    }
                                    {
                                        (isYTVideo || isVimeoVideo || isYTLive || isSelfHostedVideo) && (
                                            <SelectControl
                                                label={__("Video Size")}
                                                labelPosition='side'
                                                value={videosize}
                                                options={[
                                                    { label: 'Fixed', value: 'fixed' },
                                                    { label: 'Responsive', value: 'responsive' },
                                                ]}
                                                onChange={(videosize) => setAttributes({ videosize })}
                                                __nextHasNoMarginBottom
                                            />
                                        )
                                    }

                                    {
                                        ((!isYTVideo && !isYTLive && !isVimeoVideo && !isSelfHostedVideo) || (videosize == 'fixed')) && (
                                            <p>{__("You can adjust the width and height of embedded content.")}</p>
                                        )
                                    }



                                    {
                                        ((isYTVideo || isVimeoVideo || isYTLive || isSelfHostedVideo) && (videosize == 'responsive')) && (
                                            <p>{__("You can adjust the width of embedded content.", "embedpress")}</p>
                                        )
                                    }

                                    <TextControl
                                        label={__("Width")}
                                        value={width}
                                        onChange={(width) => {
                                            (isVimeoVideo || isYTVideo || isYTLive || isSelfHostedVideo) ? (
                                                setAttributes({
                                                    width: `${Math.round(width)}`,
                                                    height: `${roundToNearestFive(Math.round((width * 9) / 16))}`
                                                })
                                            ) : (
                                                setAttributes({ width })
                                            )
                                        }}
                                    />

                                    {
                                        ((!isYTVideo && !isVimeoVideo && !isYTLive && !isSelfHostedVideo) || (videosize == 'fixed')) && (
                                            <TextControl
                                                label={__("Height")}
                                                value={height}
                                                onChange={(height) => {
                                                    {
                                                        (isVimeoVideo || isYTVideo || isYTLive || isSelfHostedVideo) ? (
                                                            setAttributes({
                                                                height: `${Math.round(height)}`,
                                                                width: `${roundToNearestFive(Math.round((height * 16) / 9))}`
                                                            })
                                                        ) : (
                                                            setAttributes({ height })
                                                        )
                                                    }
                                                }}
                                            />
                                        )
                                    }

                                    {
                                        (isYTVideo) && (
                                            <div className={'ep-tips-and-tricks'}>
                                                {EPIcon}
                                                <a href="#" target={'_blank'} onClick={(e) => { e.preventDefault(); addTipsTrick(e) }}> {__("Tips & Tricks", "embedpress")} </a>
                                            </div>
                                        )
                                    }
                                </div>

                                {
                                    !isYTLive && (
                                        <Youtube attributes={attributes} setAttributes={setAttributes} isYTChannel={isYTChannel} />
                                    )

                                }

                                {
                                    isInstagramFeed(url) && (
                                        <div className='instafeed-controls'>
                                            <SelectControl
                                                label={__("Layout")}
                                                value={instaLayout}
                                                options={[
                                                    { label: 'Grid', value: 'insta-grid' },
                                                    { label: 'Masonary', value: 'insta-masonary' },
                                                    { label: 'Carousel', value: 'insta-carousel' },
                                                ]}
                                                onChange={(instaLayout) => setAttributes({ instaLayout })}
                                                __nextHasNoMarginBottom
                                            />

                                            <SelectControl
                                                label={__("Slides to Show")}
                                                value={slidesShow}
                                                options={[
                                                    { label: '1', value: '1' },
                                                    { label: '2', value: '2' },
                                                    { label: '3', value: '3' },
                                                    { label: '4', value: '4' },
                                                    { label: '5', value: '5' },
                                                    { label: '6', value: '6' },
                                                    { label: '7', value: '7' },
                                                    { label: '8', value: '8' },
                                                    { label: '9', value: '9' },
                                                    { label: '10', value: '10' },
                                                ]}
                                                onChange={(slidesShow) => setAttributes({ slidesShow })}
                                                __nextHasNoMarginBottom
                                            />


                                            <ToggleControl
                                                label={__("Autoplay")}
                                                checked={carouselAutoplay}
                                                onChange={(carouselAutoplay) => setAttributes({ carouselAutoplay })}
                                            />
                                            <TextControl
                                                label={__("Autoplay Speed")}
                                                value={autoplaySpeed}
                                                onChange={(autoplaySpeed) => setAttributes({ autoplaySpeed })}
                                            />
                                            <TextControl
                                                label={__("Transition Speed")}
                                                value={transitionSpeed}
                                                onChange={(transitionSpeed) => setAttributes({ transitionSpeed })}
                                            />

                                            <ToggleControl
                                                label={__("Loop")}
                                                checked={carouselLoop}
                                                onChange={(carouselLoop) => setAttributes({ carouselLoop })}
                                            />

                                            <TextControl
                                                label={__("Space")}
                                                value={carouselSpacing}
                                                onChange={(carouselSpacing) => setAttributes({ carouselSpacing })}
                                            />

                                            <ToggleControl
                                                label={__("Arrows")}
                                                checked={carouselArrows}
                                                onChange={(carouselArrows) => setAttributes({ carouselArrows })}
                                            />

                                        </div>
                                    )
                                }


                            </PanelBody>

                            <Youtube attributes={attributes} setAttributes={setAttributes} isYTVideo={isYTVideo} isYTLive={isYTLive} />
                            <Youtube attributes={attributes} setAttributes={setAttributes} />

                            <SlefHosted attributes={attributes} setAttributes={setAttributes} />


                            <Wistia attributes={attributes} setAttributes={setAttributes} isWistiaVideo={isWistiaVideo} />
                            <Vimeo attributes={attributes} setAttributes={setAttributes} isVimeoVideo={isVimeoVideo} />

                            <LockControl attributes={attributes} setAttributes={setAttributes} />
                            <ContentShare attributes={attributes} setAttributes={setAttributes} />
                        </div>
                    )
                }

                <OpenSea attributes={attributes} setAttributes={setAttributes} isOpensea={isOpensea} isOpenseaSingle={isOpenseaSingle} />

            </InspectorControls >
        )
    )
}