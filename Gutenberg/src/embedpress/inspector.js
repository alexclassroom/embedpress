import { useState } from 'react';
import Youtube from './InspectorControl/youtube';
import OpenSea from './InspectorControl/opensea';
import Wistia from './InspectorControl/wistia';
import Vimeo from './InspectorControl/vimeo';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const {
    TextControl,
    PanelBody,
    SelectControl,
} = wp.components;

const {
    InspectorControls
} = wp.blockEditor;


export default function Inspector({ attributes, setAttributes, isYTChannel, isYTVideo, isOpensea, isOpenseaSingle, isWistiaVideo, isVimeoVideo }) {

    const {
        width,
        height,
        videosize,

        editingURL,
        embedHTML,
    } = attributes;

    return (
        !editingURL && embedHTML && (
            <InspectorControls>
                {
                    !isOpensea && !isOpenseaSingle && (
                        <frameElement>
                            <PanelBody title={__("Embeded Options")}>

                                <div>
                                    {
                                        (isYTVideo || isVimeoVideo) && (
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
                                        ((!isYTVideo && !isVimeoVideo) || (videosize == 'fixed')) && (
                                            <p>{__("You can adjust the width and height of embedded content.")}</p>
                                        )
                                    }

                                    {
                                        (videosize == 'responsive') && (
                                            <p>{__("You can adjust the width of embedded content.")}</p>
                                        )
                                    }

                                    <TextControl
                                        label={__("Width")}
                                        value={width}
                                        onChange={(width) =>
                                           {
                                                (isVimeoVideo || isYTVideo) ? (
                                                    setAttributes({
                                                        width: `${Math.round(width)}`,
                                                        height: `${Math.round((width * 9) / 16)}`
                                                    })
                                                ) : (
                                                    setAttributes({width})
                                                )  
                                           }
                                        }
                                    />

                                    {
                                        ((!isYTVideo || !isVimeoVideo) && (videosize == 'fixed')) && (
                                            <TextControl
                                                label={__("Height")}
                                                value={height}
                                                onChange={(height) => {
                                                    
                                                    {
                                                        (isVimeoVideo || isYTVideo) ? (
                                                            setAttributes({
                                                                height: `${Math.round(height)}`,
                                                                width: `${Math.round((height * 16) / 9)}`
                                                            })
                                                        ) : (
                                                            setAttributes({height})
                                                        )  
                                                   }
                                                }
                                                    
                                                }
                                            />
                                        )
                                    }

                                </div>
                                <Youtube attributes={attributes} setAttributes={setAttributes} isYTChannel={isYTChannel} />
                            </PanelBody>

                            <Youtube attributes={attributes} setAttributes={setAttributes} isYTVideo={isYTVideo} />
                            <Wistia attributes={attributes} setAttributes={setAttributes} isWistiaVideo={isWistiaVideo} />
                            <Vimeo attributes={attributes} setAttributes={setAttributes} isVimeoVideo={isVimeoVideo} />
                        </frameElement>
                    )
                }

                <OpenSea attributes={attributes} setAttributes={setAttributes} isOpensea={isOpensea} isOpenseaSingle={isOpenseaSingle} />

            </InspectorControls>
        )
    )
}