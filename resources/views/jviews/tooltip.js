
export function tooltipComponent(data, container, $, builder)
{

    let inst = this, out, position = {
        bottom: (data?.position?.bottom ? " bottom" : ""),
        right: (data?.position?.right ? " right" : ""),
        left: (data?.position?.left ? " left" : "")
    };

    out = `
    <div class="relative inline-block">
        <span class="dots"></span>
        <nav class="tooltip${position.bottom}${position.right}${position.left}">
            <aside>
                ${content()}
            </aside>
        </nav>
    </div>
    `;

    function content()
    {
        let out = '';
        out += `${(typeof data.message === "string") ? '<div class="p-15">'+data.message+'</div>' : ''}`;
        if (typeof data.feed === "object") {
            out += '<ul>';
            $.each(data.feed, function (i, r) {
                out += `<li><a href="${r.url ? r.url : '#'}"${attr(r.attr)}>${r.title}</a></li>`;
            });
            out += '</ul>';
        }
        return out;
    }

    function attr(attrObj)
    {
        let out = "";
        if (typeof attrObj == "object") {
            $.each(attrObj, function (key, val) {
                        out += ` ${key}="${val}"`;
            });
        }
        return out;
    }

    
    return out;
}