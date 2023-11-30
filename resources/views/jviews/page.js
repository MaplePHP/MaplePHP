
export function pageComponent(data, container)
{
    let out = `
    <section class="holder">
        <h1>${data.headline}</h1>
        <p>${data.content}</p>
    </section>
    `;
    return out;
}