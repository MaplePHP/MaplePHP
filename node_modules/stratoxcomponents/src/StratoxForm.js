
export function StratoxForm(data, container, helper, builder)
{
    let out = '';

    out += `<form action="${data.action}" method="${data.method}">`;
    builder.groupFactory(function (o, val) {
        out += o;
    });
    out += `<input class="inp-csrf-token" type="hidden" name="csrfToken" value="${data.token}">`;

    if(typeof data.submit === "string") {
        out += `<input class="button" type="submit" name="submit" value="${data.submit}">`;
    }
    
    out += `</form>`;
    return out;
}