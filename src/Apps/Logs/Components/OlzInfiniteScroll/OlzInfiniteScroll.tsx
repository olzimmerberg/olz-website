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
    const [items, setItems] = React.useState<Item[]>([]);
    const [_prevQuery, setPrevQuery] = React.useState<Query|null>(null);
    const [_nextQuery, setNextQuery] = React.useState<Query|null>(null);

    React.useEffect(() => {
        props.fetch(props.initialQuery)
            .then((result) => {
                setItems(result.items);
                setPrevQuery(result.prevQuery);
                setNextQuery(result.nextQuery);
            });
    }, [props.initialQuery]);

    return (
        <div>
            {items.map((item) => props.renderItem(item))}
        </div>
    );
};
