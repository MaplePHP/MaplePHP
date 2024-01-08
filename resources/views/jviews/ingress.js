
export function ingressComponent(data, container, $, builder)
{
    let out = `
    <header class="holder center">
        <h1>${data.headline}</h1>
        <p>${data.content}</p>
    </header>
    `;
    return out;
}
