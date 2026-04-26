import React from 'react';
import {codeHref, type UserConstant, user as currentUser, childUsers} from '../../../Utils/constants';
import {olzCustomReaction} from '../OlzCustomReactionDialog/OlzCustomReactionDialog';
import {initOlzUserInfoModal} from '../../../Users/Components/OlzUserInfoModal/OlzUserInfoModal';

interface OlzReaction {
    userId: number;
    name: string | null;
    emoji: string;
}

interface OlzEditableReactionsProps {
    defaultEmojis?: Array<string>,
    listFn: () => Promise<Array<OlzReaction>>,
    toggleFn: (userId: number | undefined, emoji: string) => Promise<OlzReaction | null>,
}

export const OlzEditableReactions = (props: OlzEditableReactionsProps): React.ReactElement => {
    const [reactions, setReactions] = React.useState<Array<OlzReaction> | null>(null);

    React.useEffect(() => {
        props.listFn().then((newReactions) => {
            setReactions(newReactions);
        });
    }, []);

    const toggleReaction = async (userId: number | undefined, emoji: string) => {
        if (reactions === null) {
            return;
        }
        try {
            const result = await props.toggleFn(userId, emoji);
            if (result === null) {
                setReactions(reactions.filter((reaction) =>
                    reaction.userId !== userId || reaction.emoji !== emoji));
            } else {
                setReactions([...reactions, result]);
            }
        } catch {
            // ignore
        }
    };

    React.useEffect(() => {
        const onHashChange = () => {
            const match = /^#react-(.+)$/.exec(location.hash);
            if (!match) {
                return;
            }
            const emoji = decodeURIComponent(match[1]);
            toggleReaction(currentUser.id, emoji);
            location.hash = '';
        };
        window.addEventListener('hashchange', onHashChange);
        return () => {
            window.removeEventListener('hashchange', onHashChange);
        };
    }, [reactions]);

    if (reactions === null) {
        return (<></>);
    }

    const childUserById: {[userId: number]: UserConstant} = {};
    childUsers.forEach((childUser) => {
        childUserById[childUser.id ?? 0] = childUser;
    });
    const countByEmoji: {[emoji: string]: number} = {};
    (props.defaultEmojis ?? []).forEach((emoji) => {
        countByEmoji[emoji] = 0;
    });
    const nameByUser: {[userId: number]: string | null} = {};
    const emojisByUser: {[userId: number]: Array<string>} = {};
    const isActiveByEmojiByUser: {[userId: number]: {[emoji: string]: boolean}} = {};
    reactions.forEach((reaction) => {
        countByEmoji[reaction.emoji] ??= 0;
        countByEmoji[reaction.emoji]++;
        nameByUser[reaction.userId] = reaction.name;
        emojisByUser[reaction.userId] ??= [];
        emojisByUser[reaction.userId].push(reaction.emoji);
        if (
            reaction.userId === currentUser.id
            || (childUserById[reaction.userId] ?? false)
        ) {
            isActiveByEmojiByUser[reaction.userId] ??= {};
            isActiveByEmojiByUser[reaction.userId][reaction.emoji] = true;
        }
    });
    const orderedEmojis = Object.keys(countByEmoji);
    orderedEmojis.sort((a, b) => countByEmoji[b] - countByEmoji[a]);

    const reactionsForUser = (user?: UserConstant) => {
        const userName = user?.name ? `${user?.name}: ` : '';

        const emojiButtons = orderedEmojis.map((emoji) => {
            const isActive = user?.id ? ((isActiveByEmojiByUser[user.id] ?? {})[emoji] ?? false) : false;
            const activeClass = isActive ? ' active' : '';
            if (!user?.id) {
                return (
                    <a
                        href='#login-dialog'
                        className={`reaction${activeClass}`}
                        key={emoji}
                    >
                        {emoji} {countByEmoji[emoji]}
                    </a>
                );
            }
            return (
                <a
                    onClick={() => toggleReaction(user?.id, emoji)}
                    className={`reaction${activeClass}`}
                    key={emoji}
                >
                    {emoji} {countByEmoji[emoji]}
                </a>
            );
        });

        const addButton = user?.id ? (
            <button
                id='add-reaction-button'
                className='btn btn-sm btn-secondary'
                onClick={() => olzCustomReaction((emoji) => toggleReaction(user?.id, emoji))}
                key='add-button'
            >
                <img src={`${codeHref}assets/icns/new_white_16.svg`} className='noborder' />
            </button>
        ) : null;

        return (
            <div className='reactions'>
                {userName}{emojiButtons} {addButton}
            </div>
        );
    };

    const myReactions = reactionsForUser(currentUser);

    const childReactions = childUsers.map((childUser) => reactionsForUser(childUser));

    const userOverview = [];
    if (currentUser.id) {
        for (const userId in Object.keys(emojisByUser)) {
            const emojis = emojisByUser[userId];
            emojis.sort((a, b) => countByEmoji[b] - countByEmoji[a]);
            userOverview.push(<div key={`user-${userId}`}>
                <a
                    onClick={() => initOlzUserInfoModal(Number(userId))}
                    className='olz-user-info-modal-trigger name'
                >
                    {nameByUser[userId] ?? '?'}
                </a>
                : {emojis.join(' ')}
            </div>);
        }

        document.querySelectorAll('a[href^=\'#react-\']').forEach((elem) => {
            elem.classList.remove('active');
        });
        (emojisByUser[currentUser.id ?? 0] ?? []).forEach((emoji) => {
            const selector = `a[href^='#react-${encodeURIComponent(emoji)}']`;
            document.querySelectorAll(selector).forEach((elem) => {
                elem.classList.add('active');
            });
        });
    }

    return (<>
        {myReactions}
        {childReactions}
        {userOverview}
    </>);
};
