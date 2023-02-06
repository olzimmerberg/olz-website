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
    const [_prevQuery, setPrevQuery] = React.useState<Query|null>(null);
    const [_nextQuery, setNextQuery] = React.useState<Query|null>(null);

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

    return (
        <div style={{opacity: isLoading ? 0.5 : 1}}>
            {items.map((item) => props.renderItem(item))}
        </div>
    );
};
