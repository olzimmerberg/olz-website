import React from 'react';

export interface OlzInfiniteScrollProps<Item, Query> {
    fetch: (query: Query) => Promise<{
        items: Item[],
        prevQuery: Query|null,
        nextQuery: Query|null,
    }>;
    initialQuery: Query;
    renderItem: (item: Item) => React.ReactElement;
    /**
     * Start loading more content, when the end is nearer than `autoloadMargin`
     * times the window height. Default value is 4.
     */
    autoloadMargin?: number;
}

export const OlzInfiniteScroll = <Item, Query>(
    props: OlzInfiniteScrollProps<Item, Query>,
): React.ReactElement => {
    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    const [items, setItems] = React.useState<Item[]>([]);
    const [prevQuery, setPrevQuery] = React.useState<Query|null>(null);
    const [nextQuery, setNextQuery] = React.useState<Query|null>(null);
    const [isPrevLoading, setIsPrevLoading] = React.useState<boolean>(false);
    const [isNextLoading, setIsNextLoading] = React.useState<boolean>(false);
    const [containerHeight, setContainerHeight] = React.useState<number>();

    const container = React.useRef<HTMLDivElement>(null);
    const prevButton = React.useRef<HTMLButtonElement>(null);
    const nextButton = React.useRef<HTMLButtonElement>(null);

    React.useEffect(() => {
        setIsLoading(true);
        const timeoutId = window.setTimeout(() => {
            const query = props.initialQuery;
            props.fetch(query)
                .then((result) => {
                    if (query !== props.initialQuery) {
                        return; // Drop the result; it's already outdated!
                    }
                    setItems(result.items);
                    setPrevQuery(result.prevQuery);
                    setNextQuery(result.nextQuery);
                    setTimeout(() => {
                        setIsLoading(false);
                        setContainerHeight(container.current?.offsetHeight);
                        document.getElementById('initial-scroll')?.scrollIntoView({
                            behavior: 'instant' as ScrollBehavior,
                        });
                    }, 1);
                });
        }, 300);
        return () => {
            window.clearTimeout(timeoutId);
        };
    }, [props.initialQuery]);

    const loadPrevious = React.useCallback(() => {
        if (!prevQuery) {
            return;
        }
        setIsPrevLoading(true);
        const query = prevQuery;
        props.fetch(query)
            .then((result) => {
                if (query !== prevQuery) {
                    return; // Drop the result; it's already outdated!
                }
                setItems([...result.items, ...items]);
                setPrevQuery(result.prevQuery);
                setTimeout(() => {
                    setIsPrevLoading(false);
                    const newHeight = container.current?.offsetHeight;
                    const oldHeight = containerHeight;
                    setContainerHeight(newHeight);
                    if (oldHeight && newHeight) {
                        window.scrollBy({
                            top: newHeight - oldHeight,
                            behavior: 'instant' as ScrollBehavior,
                        });
                    }
                }, 1);
            });
    }, [items, containerHeight, prevQuery]);

    const loadNext = React.useCallback(() => {
        if (!nextQuery) {
            return;
        }
        setIsNextLoading(true);
        const query = nextQuery;
        props.fetch(query)
            .then((result) => {
                if (query !== nextQuery) {
                    return; // Drop the result; it's already outdated!
                }
                setItems([...items, ...result.items]);
                setNextQuery(result.nextQuery);
                setTimeout(() => {
                    setIsNextLoading(false);
                    const newHeight = container.current?.offsetHeight;
                    setContainerHeight(newHeight);
                }, 1);
            });
    }, [items, containerHeight, nextQuery]);

    React.useEffect(() => {
        const autoloadMargin = props.autoloadMargin ?? 4;
        const intervalId = window.setInterval(() => {
            const prevRect = prevButton.current?.getBoundingClientRect();
            const isNearPrevButton = prevRect
                && prevRect.bottom > -autoloadMargin * window.innerHeight;
            if (isNearPrevButton && !isPrevLoading && !isLoading) {
                loadPrevious();
            }
            const nextRect = nextButton.current?.getBoundingClientRect();
            const isNearNextButton = nextRect
                && nextRect.top < (autoloadMargin + 1) * window.innerHeight;
            if (isNearNextButton && !isNextLoading && !isLoading) {
                loadNext();
            }
        }, 1000);
        return () => {
            window.clearInterval(intervalId);
        };
    }, [props.autoloadMargin, isLoading, isPrevLoading, isNextLoading, loadPrevious, loadNext]);

    return (
        <div
            ref={container}
            style={{opacity: isLoading ? 0.5 : 1}}
        >
            {prevQuery ? (
                <button
                    type='button'
                    ref={prevButton}
                    className='btn btn-outline-primary'
                    disabled={isPrevLoading}
                    onClick={loadPrevious}
                >
                    mehr...
                </button>
            ) : null}
            {items.map((item) => props.renderItem(item))}
            {nextQuery ? (
                <button
                    type='button'
                    ref={nextButton}
                    className='btn btn-outline-primary'
                    disabled={isNextLoading}
                    onClick={loadNext}
                >
                    mehr...
                </button>
            ) : null}
        </div>
    );
};
