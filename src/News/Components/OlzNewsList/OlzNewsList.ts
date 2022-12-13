let invisiblePageContents: string[] = [];
let lastShownPageIndex = 0;

window.addEventListener('load', () => {
    const page0 = document.getElementById('news-list-page-0');
    if (!page0) {
        return;
    }
    reconsiderShowingNextPage();
    window.addEventListener('scroll', () => {
        reconsiderShowingNextPage();
    });
    setInterval(() => {
        reconsiderShowingNextPage();
    }, 1000);
});

function reconsiderShowingNextPage(): void {
    const pageIndex = lastShownPageIndex;
    const page = document.getElementById(`news-list-page-${pageIndex}`);
    if (!page) {
        return;
    }
    const pageRect = page.getBoundingClientRect();
    const spaceRemaining = pageRect.bottom - window.innerHeight;
    if (spaceRemaining > 500) {
        return;
    }
    const nextPageIndex = pageIndex + 1;
    const nextPage = document.getElementById(`news-list-page-${nextPageIndex}`);
    if (!nextPage) {
        return;
    }
    const invisiblePageContent = invisiblePageContents[nextPageIndex - 1];
    if (!invisiblePageContent) {
        setTimeout(reconsiderShowingNextPage, 100);
        return;
    }
    nextPage.innerHTML = invisiblePageContent;
    lastShownPageIndex = nextPageIndex;
}

export function olzNewsListSetInvisiblePageContents(pageContents: string[]): void {
    invisiblePageContents = pageContents;
}
