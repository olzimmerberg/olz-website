import React from 'react';
import {codeHref, type UserConstant, user as currentUser, childUsers} from '../../../Utils/constants';
import {olzCustomReaction} from '../OlzCustomReactionDialog/OlzCustomReactionDialog';
import {initOlzUserInfoModal} from '../../../Users/Components/OlzUserInfoModal/OlzUserInfoModal';

import './OlzEditableReactions.scss';

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
            // TODO: Check if it actually was an HTTP 403 error
            location.hash = '#login-dialog';
        }
    };

    React.useEffect(() => {
        const onHashChange = () => {
            const match = /^#react-(.+)$/.exec(location.hash);
            if (!match) {
                return;
            }
            if (!currentUser.id) {
                location.hash = '#login-dialog';
                return;
            }
            const emoji = decodeURIComponent(match[1]);
            toggleReaction(currentUser.id, emoji);
            history.replaceState(null, '', window.location.pathname);
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
    const userIdSet = new Set<number>([]);
    const nameByUser: {[userId: number]: string | null} = {};
    const emojisByUser: {[userId: number]: Set<string>} = {};
    const isActiveByEmojiByUser: {[userId: number]: {[emoji: string]: boolean}} = {};
    reactions.forEach((reaction) => {
        countByEmoji[reaction.emoji] ??= 0;
        countByEmoji[reaction.emoji]++;
        userIdSet.add(reaction.userId);
        nameByUser[reaction.userId] = reaction.name;
        emojisByUser[reaction.userId] ??= new Set();
        emojisByUser[reaction.userId].add(reaction.emoji);
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

    const userRows = [];
    if (currentUser.id) {
        const userIds = [...userIdSet].sort((a, b) =>
            (nameByUser[a] ?? '').localeCompare(nameByUser[b] ?? ''));
        for (const userId of userIds) {
            const emojis = emojisByUser[userId] ?? new Set();
            userRows.push(
                <tr>
                    <td className='name-col'>
                        <a
                            onClick={() => initOlzUserInfoModal(Number(userId))}
                            className='olz-user-info-modal-trigger name'
                        >
                            {nameByUser[userId] ?? '?'}
                        </a>
                    </td>
                    {orderedEmojis.map((emoji) => <td>{emojis.has(emoji) ? emoji : ''}</td>)}
                </tr>,
            );
        }

        document.querySelectorAll('a[href^=\'#react-\']').forEach((elem) => {
            elem.classList.remove('active');
            const emoji = decodeURIComponent(elem.getAttribute('href')?.substring(7) ?? '');
            const emojiCount = countByEmoji[emoji] ?? '';
            let infoElem = elem.querySelector('span.reaction-info');
            if (!infoElem) {
                infoElem = document.createElement('span');
                infoElem.classList.add('reaction-info');
                elem.appendChild(infoElem);
            }

            infoElem.innerHTML = `${emoji}&nbsp;${emojiCount}`;
            console.log(emoji, infoElem);
        });
        (emojisByUser[currentUser.id ?? 0] ?? []).forEach((emoji) => {
            const selector = `a[href^='#react-${encodeURIComponent(emoji)}']`;
            document.querySelectorAll(selector).forEach((elem) => {
                elem.classList.add('active');
            });
        });
    }

    const allReactionsTable = userRows.length > 0 ? (
        <div className='table-container'>
            <table className='boxy' style={{width: 'auto'}}>
                <thead>
                    <tr>
                        <th>Name</th>
                        {orderedEmojis.map((emoji) => <th>{emoji}</th>)}
                    </tr>
                </thead>
                {userRows}
            </table>
        </div>
    ) : null;

    return (
        <div className='olz-editable-reactions'>
            {myReactions}
            {childReactions}
            {allReactionsTable}
        </div>
    );
};
