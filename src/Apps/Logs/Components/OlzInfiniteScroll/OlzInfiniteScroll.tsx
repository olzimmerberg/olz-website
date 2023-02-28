import React from 'react';

export interface OlzInfiniteScrollProps<Item, Query> {
    fetch: (query: Query) => Promise<{
        items: Item[],
        prevQuery: Query|null,
        nextQuery: Query|null,
    }>;
    initialQuery: Query;
    renderItem: (item: Item) => React.ReactElement;
}

export const OlzInfiniteScroll = <Item, Query>(
    props: OlzInfiniteScrollProps<Item, Query>,
): React.ReactElement => {
    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    const [items, setItems] = React.useState<Item[]>([]);
    const [prevQuery, setPrevQuery] = React.useState<Query|null>(null);
    const [nextQuery, setNextQuery] = React.useState<Query|null>(null);

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
                    setIsLoading(false);
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
        setIsLoading(true);
        props.fetch(prevQuery)
            .then((result) => {
                // TODO: Drop the result if it's already outdated!
                setItems([...result.items, ...items]);
                setPrevQuery(result.prevQuery);
                setIsLoading(false);
            });
    }, [items, prevQuery]);

    const loadNext = React.useCallback(() => {
        if (!nextQuery) {
            return;
        }
        setIsLoading(true);
        props.fetch(nextQuery)
            .then((result) => {
                // TODO: Drop the result if it's already outdated!
                setItems([...items, ...result.items]);
                setNextQuery(result.nextQuery);
                setIsLoading(false);
            });
    }, [items, nextQuery]);

    return (
        <div style={{opacity: isLoading ? 0.5 : 1}}>
            {prevQuery ? (
                <button
                    type='button'
                    className='btn btn-outline-primary'
                    onClick={loadPrevious}
                >
                    mehr...
                </button>
            ) : null}
            {items.map((item) => props.renderItem(item))}
            {nextQuery ? (
                <button
                    type='button'
                    className='btn btn-outline-primary'
                    onClick={loadNext}
                >
                    mehr...
                </button>
            ) : null}
        </div>
    );
};
