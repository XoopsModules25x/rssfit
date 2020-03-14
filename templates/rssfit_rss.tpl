<?xml version="1.0" encoding="<{$rss_encoding}>"?>
<?xml-stylesheet href="rss.css" type="text/css"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
<{foreach from=$feed.channel key='ch_key' item='ch_var'}>
        <<{$ch_key}>><{$ch_var}></<{$ch_key}>>
<{/foreach}>
<atom:link href="<{$xoops_url}><{$smarty.server.REQUEST_URI}>" rel="self" type="application/rss+xml" />
<{if $feed.image != ''}>
        <image>
            <title><{$feed.image.title}></title>
            <url><{$feed.image.url}></url>
            <link><{$feed.image.link}></link>
        </image>
<{/if}>
<{if $feed.sticky != ''}>
        <item>
            <title><{$feed.sticky.title}></title>
            <description><{$feed.sticky.description}></description>
            <pubDate><{$feed.sticky.pubdate}></pubDate>
            <link><{$feed.sticky.link}></link>
        </item>
<{/if}>
<{if $feed.items != ''}><{foreach item=item from=$feed.items}>
        <item>
            <title><{$item.title}></title>
            <description><{$item.description}></description>
            <pubDate><{$item.pubdate}></pubDate>
            <link><{$item.link}></link>
                <{if $item.category != ""}>
                            <category<{if $item.domain != ""}> domain="<{$item.domain}>"<{/if}>><{$item.category}></category>
                <{/if}>
                <{if $item.guid != ""}>
                            <guid><{$item.guid}></guid>
                <{/if}>
                <{if $item.extras != ''}>
                    <{foreach from=$item.extras key='exk' item='exv'}>
                                <<{$exk}>
                        <{if $exv.attributes != ''}>
                                <{foreach from=$exv.attributes key='atk' item='atv'}>
                                                <{$atk}>="<{$atv}>"
                                <{/foreach}>
                        <{/if}>
                        <{if $exv.content != ''}>
                                    ><{$exv.content}></<{$exk}>>
                        <{else}>
                                    />
                        <{/if}>
                    <{/foreach}>
                <{/if}>
        </item>
<{/foreach}><{/if}>
    </channel>
</rss>
